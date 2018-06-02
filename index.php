<?php
include('settings.php');
include(DIR_ROOT . '/views/view_temperature.php');
?>
<html>
<head>
  <title>Smart Home</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<div class="temperatures">
<?php
$view = new ViewTemperature();
echo $view->sensorsLastReading();
?>
</div>
</body>
</html>

