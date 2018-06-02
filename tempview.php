<link rel="stylesheet" href="/style.css" />
<?php
include('settings.php');
include(DIR_ROOT . '/views/view_temperature.php');
?>
<div class="menu">
<?php
echo a('Back', '/');
?>
</div>
<?php
$view = new ViewTemperature();
echo $view->listLogs(get('id', 201));
