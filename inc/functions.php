<?php

function get($k, $default) {
  return isset($_GET[$k]) ? $_GET[$k] : $default;
}

function a($title, $href, $params) {
  $query = array();
  $q = '';
  if (isset($params['query'])) {
    foreach($params['query'] as $k=>$v) $query[$k]="$k=$v";
    $q = '?' . implode('&', $query);
  }
  $out = '<a href="' . $href . $q . '">' . $title . '</a>' . "\n";
  return $out;
}

function list_items($items) {
  $out = '';
  foreach($items as $i) {
    $out .= "\t<li>" . $i . "</li>\n";
  }
  return $out;
}

function ul($items) {
  return "<ul>\n" . list_items($items) . "</ul>\n";
}

function ol($items) {
  return "<ol>\n" . list_items($items) . "</ol>\n";
}

function h($text, $nr = 3) {
  return "<h$nr>$text</h$nr>\n";
}

function div($content, $params=array()) {
  $classes = '';
  if(isset($params['classes'])) {
    $classes = ' class="' . implode(' '. $params['classes']) . '"';
  }
  return "<div$classes>$content</div>\n";
}
?>
