<?php
require_once(dirname(__FILE__) . '/../settings.php');
require_once(DIR_INC . '/analogutils.php');

global $settings;

$au = new AnalogUtils();
$line = $au->lastLogLine();
if (!$line) die("Errors:" . $au->getErrors());
$values = $au->parseLogLine($line);

$set = $settings['sensors']['analog'];
foreach($values as $k=>$v) {
  $skey = strtolower($k);
  if(isset($set[$skey])) {
    if (!$au->write_dynamics($skey, time(), $v)) {
      echo $au->getErrors() . "\n";
    }
    else {
      echo $au->getMessages();
    }
  }
}

