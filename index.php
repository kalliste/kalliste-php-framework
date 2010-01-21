<?php

require_once("config.php");

require_once("includes/general/request.php");


function debug_messages() {
  $show_queries = config('show_queries');
  $messages = array();
  if ($show_queries) {
    $messages = dump_messages("sql_queries");
  }
  return $messages; 
}


function app_flow_control() {
  $action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : "";
  if ($action == "") {
    $action = "login_page";
  }
  elseif (!in_array($action, available_actions())) {
    dbg($action, "action does not exist");
    $action = "login_page";
  }
  $return = execute_action($action);
  if (is_array($return)) {
    print json_encode($return);
  }
  else {
    print $return;
  }
}


app_flow_control();

  
?>
