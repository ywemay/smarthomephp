<?php
/**
 * @file
 * listens for security reports.
 */
include(dirname(__FILE__) . '/settings.php');
include(DIR_INC . '/functions.php');

if ($_SERVER['REMOTE_ADDR'] != BOARD_IP) {
  exit;
}
$sec_dir = DIR_DATA . '/security';

// directory shall be created manually and write permissions shall be given.
//if (!is_dir($sec_dir)) mkdir($sec_dir, 0777);
$log_file = $sec_dir . '/SEC' . date('Ymd') . '.log';
$log = date("H:i:s") . ' ' .  get('r', 'ERROR');
`echo "$log" >> "$log_file"`;

foreach($_SERVER as $k=>$v) {
  `echo "$k = $v" >> "$secdir/temp.log"`;
}
