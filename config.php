<?php


function config($var='') {
  $values = array(

    'system_mail_from' => '',
    'base_url' => '',
    'db_user' => '',
    'db_pass' => '',
    'db_name' => '',
    'db_host' => '',
    'capture_redirects' => 0, 
    'show_queries' => 1
	
  );
  if (array_key_exists($var, $values)) { return $values[$var]; }
  if ($var == '') { return $values; } else { return false; }
}


function template_config() {
  return array('base_dir' => "", 'template' => 'templates', 'compile' => 'templates_c', 'cache' => 'cache', 'config' => 'configs');
}


//error_reporting(1);

require_once("includes/db/base.php");

$db =& getdb(config('db_host'), config('db_user'), config('db_pass'), config('db_name'));


?>
