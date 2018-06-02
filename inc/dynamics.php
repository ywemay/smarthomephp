<?php
require_once(DIR_INC . '/basicmodel.php');

Class Dynamics extends BasicModel {

  var $re_raw_line = "/(\w+)\:(\-?\d+)\;/"; // resular aexpession used to parse raw data lines
  var $data_subdir = 'default';
  var $file_last_log = FALSE;

  /**
   * Loads last log line and checks if line is valid.
   * @return $line
   */
  function lastLogLine() {
    if (!$this->file_last_log) $this->file_last_log = DIR_DATA . '/' . $this->data_subdir . '_last.log';
    if (!file_exists($this->file_last_log)) return $this->error("Cannot find file " . $this->file_last_log);
    $line = trim(current(file($this->file_last_log)));
    $re ="/^(\d\d\d\d)\-(\d\d)-(\d\d) (\d\d)\:\d\d\:\d\d\: OK\!/";
    if (!preg_match($re, $line, $mt)) {
      return $this->err("The line $line does not match the expression: $re");
    }
    return $line;
  }

  /**
   * Parse a raw data line into an array;
   */
  function parseLogLine($str){
    preg_match_all($this->re_raw_line, $str, $mt);
    $rez = array();
    foreach($mt[1] as $k=>$id) {
      $rez[$id] = $mt[2][$k];
    }
    return $rez;
  }


  function ensureDir(){
    $dir = DIR_DATA . '/' . $this->data_subdir;
    if (!is_dir($dir)) {
      if (!mkdir($dir)) {
        return $this->error("Faield to create directory: " . $dir);
      }
    }
    return $dir;
  }

  function ensureDeviceDir($deviceID) {
    if(!$this->ensureDir()) return FALSE;
    $dir = DIR_DATA . '/' . $this->data_subdir . '/' . $deviceID;
    if (!is_dir($dir)) {
      if (!mkdir($dir)) {
        return $this->error("Faield to create directory: " . $dir);
      }
    }
    return $dir;
  }

  function isDeviceDirWriteable($deviceID){
    $dir = $this->ensureDeviceDir($deviceID);
    if (!$dir) return FALSE;
    return is_writeable($dir) ? $dir : $this->err("Directory $dir is not writeable...");
  }

  /**
   * Calculate averages, min, max and write down in the cronologycal files.
   */
  function write_dynamics($deviceID, $time, $value) {
    $hour = date('H', $time);
    $minute = date('i', $time);

    if (!$dir = $this->isDeviceDirWriteable($deviceID)) return FALSE;
    $this->msg("Writing data to directory: " . $dir . " for device ID: " . $deviceID);

    $logfile = $dir . '/day_' . date("Ymd", $time) . ".log";
    `echo "$hour:$minute $value" >> "$logfile"`;

    $keys = array(
      'day_hourly' => array(
        'file' => $dir . '/day_hourly_' . date("Ymd", $time) . ".dat",
        'key' => date('H', $time),
      ),
      'weekly' => array(
        'file' => $dir . '/week_' . date("YW", $time) . ".dat",
        'key' => date('w', $time) ? date('w', $time) : 7,
      ),
      'monthly' => array(
        'file' => $dir . '/month_' . date("Ym", $time) . ".dat",
        'key' => date('d', $time),
      ),
      'year_weekly' => array(
        'file' => $dir . '/year_weekly_' . date("Y", $time) . ".dat",
        'key' => date('W', $time),
      ),
      'year' => array(
        'file' => $dir . '/year_' . date("Y", $time) . ".dat",
        'key' => date('m', $time),
      ),
    );

    foreach ($keys as $set) {
      $lines = file_exists($set['file']) ? file($set['file']) : array("");
      $lCount = count($lines);
      $rez = $this->calculateLine($lCount > 0 ? $lines[$lCount-1] : FALSE,
        $set['key'], $hour, $value);
      if (!$rez) continue;
      $lines[$lCount-$rez['decriment']] = $rez['value'];
      if(!file_put_contents($set['file'], implode("", $lines))) {
        $this->error("Failed to save file " . $set['file']);
      }
    }
    return $this->noErrors();
  }

  /**
   * Calculates last file line values
   */
  function calculateLine($line, $key, $hour, $value) {
    // if device returns this value - the reading failed.
    $rez = array(
      'replace' => FALSE,
      'value' => "",
    );
    //key count;min;avg;max;minday;avgday;avgnight;maxnight;
    $def = "NONE 0;9999;9999;-9999;9999;9999;9999;-9999\n";
    if (!trim($line)) {
      $line = $def;
    }

    if (preg_match("/^(.*?) ([\d\-\;]+)$/", $line, $mt)) {
      if ($mt[1] != $key) {
        preg_match("/^(.*?) ([\d\-\;]+)$/", $def, $mt);
      }
      else $rez['replace'] = TRUE;
    }
    else {
      preg_match("/^(.*?) ([\d\-\;]+)$/", $def, $mt);
    }
    $v= explode(';', $mt[2]);
    $v[0]++; //increace read count;
    $v[1] = $v[1] > $value ? $value : $v[1];
    $v[2] = $this->avg($v[5], $value);
    $v[3] = $v[3] < $value ? $value : $v[3];
    if ($hour > 6 && $hour <= 18) {
      $v[4] = $v[4] > $value ? $value : $v[4];
      $v[5] = $this->avg($v[5], $value);
    }
    else {
      $v[6] = $this->avg($v[6], $value);
      $v[7] = $v[7] < $value ? $value : $v[7];
    }
    $rez['value'] = $key . ' ' . implode(';', $v) . "\n";
    $rez['decriment'] = $rez['replace'] ? 1 : 0;
    return $rez;
  }

  /**
   * Calculates the average temperature based on 2 values,
   */
  function avg($val, $newval) {
    return $val == 9999 ? $newval : intval(($val + $newval)/2);
  }

  function getSvgGrid(){

    $grid = '<g class="grid y-grid">';
    $grid .= '<line x1="10" x2="10" y1="10" y2="160"></line>';
    $grid .= '<line x1="5" x2="390" y1="100" y2="100"></line>';
    $grid .= '</g>';
    $grid .= '<g class="sec-grid">';

    for ($y = -40; $y < 90; $y += 10) {
      $Y = 100-$y;
      $grid .= '<line x1="5" x2="390" y1="'. $Y . '" y2="' . $Y . '"></line>';
    }
    $grid .= '</g>';
    $grid .= '<g class="labels">';
    $grid .= '<text x="2" y="10">t&deg;C</text>' . "\n";
    for ($y = -40; $y < 90; $y += 10) {
      $grid .= '<text x="2" y="' . (100 - $y) . '">' . $y . '</text>' . "\n";
    }
    $grid .= '</g>';
    return $grid;
  }

  function addVertexesToGrid(&$grid, $nr = 24){
    $grid .= '<g class="sec-grid vertical-lines">' . "\n";
    for ($i = 1; $i<=$nr; $i++){
      $x= 10 + $i*10;
      $grid .= "<line x1=\"$x\" x2=\"$x\" y1=\"0\" y2=\"140\"></line>\n";
    }
    $grid .= '</g>' . "\n";
    $grid .='<g class="labels">' . "\n";
    for ($i = 1; $i<=$nr; $i++) {
      $x = 10+$i*10-($i<10 ? 2 : 3);
      $grid .= "<text x=\"$x\" y=\"140\">$i</text>\n";
    }
    $grid .= '</g>' . "\n";
  }

  /**
   * @sensorId The id of the sensor device.
   * @timeKey The number of year (2018), or month (201812), or week (201807), or day (20181230)
   * @mode day, day_hourly, week, month, year_weekly, year
   * Build an svg string to display a graph of temperature readings.
   */
  function getSvg($sensorId, $timeKey, $mode = 'week'){

    $dir = DIR_DATA . '/' . $this->data_subdir . '/' . $sensorId;
    if (!is_dir($dir)) return FALSE;

    $points = array(
      'min' => array(),
      'avg' => array(),
      'max' => array(),
    );
    $points_add = array(
      'minday' => array(),
      'avgday' => array(),
      'avgnight' => array(),
      'maxnight' => array(),
    );

    $grid = $this->getSvgGrid();

    switch($mode){
    case 'day':
      $viewBox = "0 0 260 140";
      $file_name = $dir . '/day_' . $timeKey . ".log";
      $points = array('avg' => '');
      $this->addVertexesToGrid($grid, 24);
      break;
    case 'day_hourly':
      $viewBox = "0 0 260 140";
      $file_name = $dir . '/day_hourly_' . $timeKey . ".dat";
      $points = array('avg' => '');
      $this->addVertexesToGrid($grid, 24);
      break;
    case 'week':
      $viewBox ="0 0 90 140";
      $file_name = $dir . '/week_' . $timeKey . ".dat";
      $points += $points_add;
      $this->addVertexesToGrid($grid, 7);
      break;
    case 'month':
      $viewBox = "0 0 330 140";
      $file_name = $dir . '/month_' . $timeKey . ".dat";
      $points += $points_add;
      $this->addVertexesToGrid($grid, 31);
      break;
    case 'year_weekly':
      $viewBox = "0 0 500 140";
      $file_name = $dir . '/year_weekly_' . $timeKey . ".dat";
      $points += $points_add;
      $this->addVertexesToGrid($grid, intval(366/7) + 1);
      break;
    case 'year':
      $viewBox = "0 0 140 140";
      $file_name = $dir . '/year_' . $timeKey . ".dat";
      $points += $points_add;
      $this->addVertexesToGrid($grid, 12);
      break;
    }

    $data = $this->readDataFile($file_name);

    if (!$data) {
      return "Missing file $file_name...";
    }

    $circles = array();
    $hight = 140;
    foreach($data as $k=>$val) {
      if (count($points) == 1) {
        //$points['avg'][intval($k)] = intval($k) . ',' . $val;
        $this->adjustValueToPoint($points, $circles, $k, $val, 'avg');
      }
      else {
        foreach(array('min', 'avg', 'max') as $valk) {
          $this->adjustValueToPoint($points, $circles, $k, $val, $valk);
        }
        if (count($points) > 3) {
          foreach(array('minday', 'avgday', 'avgnight', 'maxnight') as $valk) {
            //adjustValueToPoint($points, $k, $val, $valk);
          }
        }
      }
    }

    $colors = $this-> _polylineColors();
    $out = '<svg class="graph" viewBox="' . $viewBox. '">';
    $out .= $grid;
    foreach ($points as $lineK=>$data) {
      $clr = isset($colors[$lineK])?$colors[$lineK] : '#555555';
      $out .= '<polyline fill="none" stroke="'. $clr .'" stroke-width="0.8" ';
      $out .= "\npoints=\"" . implode("\n", $data) . '"/>';
      $out .= '<g class="data" data-setname="temperature">';
      if (isset($circles[$lineK])) {
        $out .= implode("\n", $circles[$lineK]) . '</g>';
      }
    }
    $out .= '</svg>';
    return $out;
  }

  /**
   * Retuns a set of web colors to use for graph polylines.
   */
  function _polylineColors(){
    return array(
      'read' => '#007700',
      'min' => '#003333',
      'avg' => '#ff8800',
      'max' => '#448855',
      'minday' => '#662233',
      'avgday' => '#cc0044',
      'avgnight' => '#55ff88',
      'maxnight' => '#883399',
    );
  }

  function adjustValueToPoint(&$points, &$circles, $k, $val, $valk) {
    $y = 140 - intval($val[$valk]/10) - 40;
    $x = 10+intval($k)*10;
    $txt = $val[$valk]/10;
    $points[$valk][intval($k)] =  $x . ',' . $y;
    $circles[$valk][intval($k)] = '<g class="data-point">
      <circle cx="' . $x . '" cy="' . $y .'" r="1"></circle> '.
      "<text x=\"$x\" y=\"" . ($y-5) . "\">$txt</text></g>";
  }

  /**
   * Reads temperature data file and loads it into an array.
   */
  function readDataFile($fname) {
    if (!file_exists($fname)) return FALSE;
    $rez = array();
    $lines = file($fname);

    foreach($lines as $line) {
      $parts = explode(' ', $line);

      if (count($parts) != 2) continue;
      $r = explode(';', $parts[1]);
      if (count($r) == 1) {
        $rez[$parts[0]]['avg'] = $r[0];
      }
      elseif (count($r) == 8) {
        $rez[$parts[0]] = array(
          'count' => $r[0],
          'min' => $r[1],
          'avg' => $r[2],
          'max' => $r[3],
          'minday' => $r[4],
          'avgday' => $r[5],
          'avgnight' => $r[6],
          'maxnight' => $r[7],
        );
      }
    }
    return $rez;
  }

}

