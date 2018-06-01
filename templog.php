<?php
include(dirname(__FILE__) . '/settings.php');
include(DIR_ROOT . '/views/view_temperature.php');
$page_title = 'Temperature Logs';
?>
<html>
<head>
  <title><?php echo $page_title;?></title>
  <link rel="stylesheet" href="/style.css" />
</head>
<body>
<?php
$view = new ViewTemperature();

echo "<h1>$page_title</h1>";
echo $view->listLogs(get('id', 201));
?>
</body>
</html>
