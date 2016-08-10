<?php

require_once("config.php");
require_once("includes/oo/app.php");
require_once("includes/models/session.php");

class sampleappApp extends kApp {

    public function preinit() {
        parent::preinit();
        session_name('sampleApp');
        session_start();
        if (session::logged_in()) {
            session::refresh();
        }
    }

}

$app = new sampleappApp;
$app->run();
  
?>
