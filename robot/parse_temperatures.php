<?php
require_once(dirname(__FILE__) . '/../settings.php');
require_once(DIR_INC . '/temputils.php');

$fname = DIR_DATA . '/temperature_last.dat';

$line = trim(current(file($fname)));
$re ="/^(\d\d\d\d)\-(\d\d)-(\d\d) (\d\d)\:\d\d\:\d\d\: OK\!/";
if (!preg_match($re, $line, $mt)) {
    echo "Readding not suitable for parsing...\n";
      exit;
}
$re = "/(\d\d\d)\:(\-?\d+)\;/";
preg_match_all($re, $line, $t);
$readings = array();
foreach ($t[1] as $k=>$id) {
  $value = $t[2][$k];
  if (abs($value) == 999) continue;
  if (abs($value) == -1270) continue;
  $sensorDir = DIR_DATA . '/temperature/' . $id;
  if (!is_dir($sensorDir)) mkdir($sensorDir, 0777, TRUE);
  recordTemperature($id, time(), $value);
  echo $id . ' = ' . $value . "\n";
}

