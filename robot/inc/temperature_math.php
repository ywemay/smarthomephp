<?php
$data_dir = $argv[1];

if (!is_dir($data_dir)) {
  echo "Failed to find the dire $data_dir...\n";
  exit;
}

$fname = $data_dir . '/temperature_last.dat';
$line = trim(current(file($fname)));

$re ="/^(\d\d\d\d)\-(\d\d)-(\d\d) (\d\d)\:\d\d\:\d\d\: OK\!/";

if (!preg_match($re, $line, $mt)) {
  echo "Readding not suitable for parsing...\n";
  exit;
}

$re = "/(\d\d\d)\:(\-?\d+)\;/";
preg_match_all($re, $line, $t);
$readings = array();
foreach ($t[1] as $k=>$id) $readings[$id] = $t[2][$k];

$time = array('hour', 'day', 'month', 'year');

$ini['hour'] = $data_dir . '/' . $mt[1] . '/' . $mt[2] . '/' . $mt[3] . '/' . $mt[4] . '_temp_stat.dat';
$ini['day'] = $data_dir . '/' . $mt[1] . '/' . $mt[2] . '/' . $mt[3] . '_temp_stat.dat';
$ini['month'] = $data_dir . '/' . $mt[1] . '/' . $mt[2] . '_temp_stat.dat';
$ini['year'] = $data_dir . '/' . $mt[1] . '_temp_stat.dat';

$path = dirname($ini['hour']);
if (!is_dir($path)) mkdir($path, 0777, TRUE);

$default = array(
  'avg' => array(),   // temperature average
  'min' => array(),   // minimum registerd temperatuew
  'max' => array(),   // maximum registerd temperature
  'read' => array(),  //number of readings taken in accout
);

foreach (array_keys($readings) as $k) {
  $default['avg'][$k] = 999;
  $default['read'][$k] = 0;
  $default['max'][$k] = -999;
  $default['min'][$k] = 999;
}

$dat = array();
foreach($time as $tk) {
  //unlink($ini[$tk]);
  $dat[$tk] = file_exists($ini[$tk]) ? parse_ini_file($ini[$tk], true) : $default;
  foreach ($readings as $id => $val) {
    if ($val == -999) continue; // bad reading
    if ($val > $dat[$tk]['max'][$id]) $dat[$tk]['max'][$id] = $val;
    if ($val < $dat[$tk]['min'][$id]) $dat[$tk]['min'][$id] = $val;
    $dat[$tk]['read'][$id]++;
    if ($dat[$tk]['avg'][$id] == 999) {
      $dat[$tk]['avg'][$id] = $val;
    }
    elseif($val > -1270) {
      $dat[$tk]['avg'][$id] = intval(($dat[$tk]['avg'][$id] + $val) / 2);
    }
  }

  $content = "";
  foreach($dat[$tk] as $sec=>$vals) {
    $content .= "[$sec]\n";
    foreach($vals as $id=>$val) {
      $content .= "$id=$val\n";
    }
    $content .="\n";
  }
  file_put_contents($ini[$tk], $content);
}


