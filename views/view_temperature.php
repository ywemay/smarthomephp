<?php

require_once(DIR_INC . '/functions.php');
require_once(DIR_INC . '/temputils.php');

class ViewTemperature {
  
  function sensorsLastReading(){
    $out = "<h3>Last temperature readings:</h3>\n";
    $fname = DIR_DATA . '/temperature_last.dat';
    if (!file_exists($fname)) {
      $out .= "<div>No data available.</div>";
      return $out;
    }

    $line = current(file($fname));
    $lineparts = explode("OK!", $line);
    $raw = end($lineparts);
    $pairs = explode(";", $raw);

    $out .= '<div class="temperature">' . "\n";

    $readings = array();

    foreach ($pairs as $pair) {
      $pair = explode(":", $pair);
      if (count($pair) < 2) continue;
      list ($id, $rawval) = $pair;
      if (!trim($id)) continue;
      $val = ($rawval/10) . '&deg;C';
      if (abs($rawval) == 999 || abs($rawval) == 127) $val = 'ERR';
      $readings[$id] = $val;
      //$out .= "<div><strong>$id:</strong> $val</div>\n";
    }

    $table = '<table class="table-temperature">' . "\n";
    $table .= "<tr><th>Sensor</th><th>Temperature</th></tr>\n";
    foreach($this->deviceSettings() as $id=>$inst) {
      $table .= "\n<tr>\n\t\t<td>$inst[title]</td>\n";
      $val = isset($readings[$id]) ? $readings[$id] : 'ERR';
      $table .= "\t\t<td class=\"temperature\">$val</td>\n";
      $table .= "\t</tr>";
    }
    $table .= "</table>\n";

    $out .= $table;

    $out .= '</div>' . "\n";
    return $out;
  }

  /**
   * Returns temperature devices settings array
   */
  function deviceSettings($id = FALSE) {
    global $settings;
    $set =  $settings['sensors']['temperature'];
    if (!$id) return $set;
    return isset($set[$id]) ? $set[$id] : FALSE;
  }

  function getSensorDir($id) {
    return DIR_DATA . '/temperature/' . $id;
  }

  /**
   * Builds links to statistics for temperatures.
   */    
  function buildIndexLinks($id, $page = FALSE) {
    if (!$page) $page = $_SERVER['PHP_SELF'];
    $rez = '';

    $y = get('y', date('Y'));
    $m = get('m', date('Ym'));
    $d = get('d', date('Ymd'));

    $dir = $this->getSensorDir($id);
    $links = array();

    $days = glob($dir . '/day_hourly_' . $m . '*.dat');
    foreach($days as $fn) {
      $dy= preg_replace("/^day\_hourly\_/", '', basename($fn, '.dat'));
      $params = array('query' => array(
        'd' => $dy,
        'id' => $id,
      ));
      $links['days'][$dy] = a(substr($dy, 6, 2), $page, $params);
    }

    $months = glob($dir . '/month_' . $y . '*.dat');
    foreach ($months as $fn) {
      $mo = preg_replace("/^month\_/", '', basename($fn, '.dat'));
      $params = array('query' => array(
        'm' => $mo,
        'id' => $id,
      ));
      $links['months'][$mo] = a(substr($mo, 4, 2), $page, $params);
    }

    $years = glob($dir . '/year_[^w]*.dat');
    foreach ($years as $fn) {
      $yr = preg_replace("/^year\_/", '', basename($fn, '.dat'));
      $params = array('query' => array(
        'y' => $yr,
        'id' => $id,
      ));
      $links['years'][$yr] = a($yr, $page, $params);
    }
    foreach($links as $lks) {
      $rez .= ul($lks);
    }
    return $rez;
  }

  /**
   * Lists available log index.
   */
  function listLogs($id){
    $out = "";
    $dir = $this->getSensorDir($id);

    $instance = $this->deviceSettings($id);
    if (!$instance) {
      return "Sensor $id is not defined in this system...";
    }
    $out .= '<h3>Logs for device ' . $instance['title'] . ' (' . $id . ')</h3>' . "\n";
    $files = glob($dir . '/month_*.dat');

    $y = get('y', date('Y'));
    $m = get('m', date('Ym'));
    $d = get('d', date('Ymd'));

    $out .= h('Day ' . $d . ', per hour');
    $out .= getTempSvg($id, $d, 'day_hourly');
    $out .= h('Month ' . $m);
    $out .= getTempSvg($id, $m, 'month');
    $out .= h('Year ' . $y . ', weekly');
    $out .= getTempSvg($id, $y, 'year_weekly');
    $out .= h('Year ' . $y);
    $out .= getTempSvg($id, $y, 'year');

    if (!$y) $y = date('Y');
    if (!$m) $m = $y == date('Y') ? date('m') : '01';
    if (!$d) $d = $m == date('m') ? date('d') : '01'; 

    $day = sprintf("%04d%02d%02d", $y, $m, $d);

    $out .= $this->buildIndexLinks($id);
    return $out;
  }

}
