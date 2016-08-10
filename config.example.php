<?php

function config($var='') {
  $values = array(

    'system_mail_from' => '',
    'base_url' => '',
    'password' => '',
    'db_user' => '',
    'db_pass' => '',
    'db_name' => '',
    'db_host' => 'localhost',
    'capture_redirects' => 0,
    'show_queries' => 1,
    'show_debug' => 1,
    'send_mail' => 1,
  );

  if (array_key_exists($var, $values)) { return $values[$var]; }
  if ($var == '') { return $values; } else { return false; }
}


function template_config() {
  return array('base_dir' => "", 'template' => 'templates', 'compile' => 'templates_c', 'cache' => 'cache', 'config' => 'configs');
}


ini_set("display_errors", 1); 
error_reporting(E_ALL & ~E_DEPRECATED);
date_default_timezone_set("America/Chicago");
require_once("includes/db/base.php");

$db = getdb('mysql:host='.config('db_host').';dbname='.config('db_name'), config('db_user'), config('db_pass'));

?>
