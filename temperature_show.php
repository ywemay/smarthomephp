<?php
$page = $_SERVER['PHP_SELF'];
$sec = "10";
header("Refresh: $sec; url=$page");


date_default_timezone_set("Europe/Bucharest");

include("inc/temputils.php");

define('WORK_DIR', dirname(__FILE__));
define('DATA_DIR', WORK_DIR . '/data');

$log_file = DATA_DIR . '/' . date('Y/m/') . date('Ymd') . '_temp.log';
$log_last = DATA_DIR . '/temperature_last.dat';

$last = trim(current(file($log_last)));
$temps = tempStringParse($last);
//print_r($temps);
$stat = loadStatDataByTime(DATA_DIR);

$header = array(
  'Nume Sensor',
  'Current',
  'Last Hour',
  'Today',
  'Month',
  'Year',
);

echo date("Y-m-d H:i");
echo "<table>\n\t<thead>\n";
foreach ($header as $ttl) echo "\t\t<th>$ttl</th>\n";
echo "\t</thead>\n\t<tbody>";

$tempdef = array(
  201 => 'Dormitor',
  202 => 'Coridor',
  203 => 'Baie',
  204 => 'Bucatarie',
  205 => 'Pod',
  206 => 'Pod sub izolatie',
  207 => 'Afara',
  208 => 'Pamint 1m',
  209 => 'Pamint 2m',
  210 => 'Groapa fantina',
  211 => 'Apa udat gradina',
  212 => 'Soba 2',
);

function getFloatTemperature($d) {
  if ($d < -1270) return 'ERR';
  if (abs(d) == 9999) return 'ERR';
  return $d/10;
}

function getFromToStr($v, $id) {
  $min = getFloatTemperature($v['min'][$id]);
  $max = getFloatTemperature($v['max'][$id]);
  $avg = getFloatTemperature($v['avg'][$id]);
  return "$min...$max";
}
$tbl = "";
foreach ($tempdef as $id=>$name) {
  $tbl .= "\t\t<tr>\n";
  $tbl .= "\t\t\t<td class='temp-name'>$name</td>\n";
  $tbl .= "\t\t\t<td>" . ($temps[$id]/10) . "&deg;C</td>\n";
  $tbl .= "\t\t\t<td>" . getFromToStr($stat['hour'], $id) . "&deg;C</td>\n";
  $tbl .= "\t\t\t<td>" . getFromToStr($stat['day'], $id) . "&deg;C</td>\n";
  $tbl .= "\t\t\t<td>" . getFromToStr($stat['month'], $id) . "&deg;C</td>\n";
  $tbl .= "\t\t\t<td>" . getFromToStr($stat['year'], $id) . "&deg;C</td>\n";
  $tbl .= "\t\t</tr>\n";
}
$tbl .="</tbody></table>";
print $tbl;
print_r(array_keys($stat['hour']));
print getDayStatSvgGraph(DATA_DIR);
