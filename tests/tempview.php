<link rel="stylesheet" href="/style.css" />
<?php
include('../settings.php');
include(DIR_ROOT . '/views/view_temperature.php');

$view = new ViewTemperature();
echo $view->sensorsLastReading();

echo "<h1>Listing test</h1>";
echo $view->listLogs(204);
