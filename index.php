<?php

require_once("config.php");
require_once("includes/oo/app.php");
require_once("includes/helpers/session.php");

class sampleappApp extends kApp {
}

session_name('sampleapp');
session_start();
refresh_session();

$app = new sampleappApp;
$app->run();
  
?>
