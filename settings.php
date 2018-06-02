<?php
global $settings;

define('DIR_ROOT', dirname(__FILE__));
define('DIR_DATA', DIR_ROOT . '/data');
define('DIR_INC', DIR_ROOT . '/inc');

define('BOARD_IP', "192.168.10.177");

$settings = array(
  'sensors' => array(
    'temperature' => array(
      201 => array(
        'title' => 'Dormitor',
      ),
      202 => array(
        'title' => 'Pamint 1m',
      ),
      203 => array(
        'title' => 'Pamint 2m',
      ),
      204 => array(
        'title' => 'Coridor',
      ),
      205 => array(
        'title' => 'Bucatarie',
      ),
      206 => array(
        'title' => 'Baie',
      ),
      207 => array(
        'title' => 'Afara',
      ),
      208 => array(
        'title' => 'Pod',
      ),
      209 => array(
        'title' => 'Tavan/captuseala',
      ),
      210 => array(
        'title' => 'Cotruta',
      ),
      211 => array(
        'title' => 'Groapa fantana',
      ),
      212 => array(
        'title' => 'Groapa robinet',
      ),
      213 => array(
        'title' => 'Apa gradinarie',
      ),
    ),
    'analog' => array(
      'light' => array(
        'title' => 'Day light',
      ),
      'moist' => array(
        'title' => 'Umeditatea aierului',
      ),
      'noisy' => array(
        'title' => 'Intensitatea sunetului',
      ),
    ),
  ),
);
?>

