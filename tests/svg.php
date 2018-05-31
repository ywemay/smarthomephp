<html>
<head>
  <title>Test Svg Temperature</title>
  <link rel="stylesheet" href="../style.css" />
</head>
<body>
<?php
include(dirname(__FILE__) . '/../settings.php');
include(DIR_INC . '/temputils.php');

?>
<h3>Month Graph:</h3>
<?php
echo getTempSvg(202, '201804', 'month');
?>
<h3>Year Graph:</h3>
<?php
echo getTempSvg(202, '2018', 'year');
?>
<h3>Week Graph:</h3>
<?php
echo getTempSvg(202, '201812', 'week');
?>
<h3>Day Hourly Graph:</h3>
<?php
echo getTempSvg(202, '20180420', 'day_hourly');
?>
<h3>Day Graph:</h3>
<?php
echo getTempSvg(202, '20180420', 'day');
?>
</body>
</html>
