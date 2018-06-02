<?php

class BasicModel {

  var $messages = array();
  var $errors = array();


  function err($str) {
    return $this->error($str);
  }

  function error($str) {
    $this->errors[] = $str;
    return FALSE;
  }

  function msg($str) {
    $this->messages[] = $str;
    return TRUE;
  }

  function getErrors(){
    return implode("\n", $this->errors);
  }

  function getHtmlErrors() {
    $out = '<div class="errors">' . "\n";
    $out .= '<div class="error">' . implode("</div>\n<div class=\"error\">", $this->errors) . "</div>\n";
    $out .= '</div>' . "\n";
    return $out;
  }

  function getMessages(){
    return implode("\n", $this->messages);
  }

  function getHtmlMessages() {
    $out = '<div class="messages">' . "\n";
    $out .= '<div class="message">' . implode("</div>\n<div class=\"message\">", $this->messages) . "</div>\n";
    $out .= '</div>' . "\n";
    return $out;
  }

  function noErrors() {
    return !$this->errors ? TRUE : FALSE;
  }

  function hasErrors() {
    return $this->errors ? TRUE : FALSE;
  }
}
