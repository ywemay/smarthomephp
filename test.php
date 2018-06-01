<?php
include('settings.php');
include('views/view_temperature.php');

$view = new ViewTemperature();
echo $view->sensorsLastReading();
