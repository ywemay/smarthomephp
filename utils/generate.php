<?php

/**
 * @file
 * Generates simulated temperature data for given device ID.
 */

require_once(dirname(__FILE__) . '/../settings.php');
require_once(DIR_INC . '/temputils.php');

if (!$argv[1]) {
  die('Device ID required as parameter...\n');
}

$deviceId = $argv[1];
$deviceDir = DIR_DATA . '/' . $deviceId;

if (!is_dir($deviceDir)) {
  mkdir($deviceDir);
  if (!is_dir($deviceDir)) {
    die("$deviceDir not a directory...\n");
  }
}

echo "Simulate detector $deviceId  work...\n";
$now = time();

$from = $now - 1 * 90 * 24 * 60 * 60;

$preview = 9999;

for ($i = $from; $i <= $now; $i+=60*30) {
  $year = Date('y', $i);
  $month = intval(Date('m', $i));
  $day = intval(Date('d', $i));
  $hour = intval(Date('H', $i));
  $minute = intval(Date('i', $i));
  $week = intval(Date('W', $i));

  switch($month) {
  case 1:
    $min = -20; $max = 0;
    break; 
  case 12:
  case 2:
    $min = -10; $max = 5;
    break; 
  case 3:
  case 11:
    $min = 0; $max = 15;
    break; 
  case 4:
  case 10:
    $min = 10; $max = 22;
    break; 
  case 5:
  case 9:
    $min = 15; $max = 26;
    break; 
  case 6:
  case 8:
    $min = 22; $max = 30;
    break; 
  case 7:
    $min = 24; $max = 32;
    break; 
  }
  if ($preview == 9999) {
    $preview = rand($min, $max);
  }
  $change = rand(-2, 2);
  if ($preview+$change>$max || $preview+$change<$min) $change = -$change;
  $preview = $preview+$change;
  $value = $preview *10 + rand(0,9);

  echo Date("Y-m-d H:i", $i) . ': ' . $value . "\r";
  recordTemperature($deviceId, $i, $value);
}
echo "\n";
echo "Done...\n";
?>
