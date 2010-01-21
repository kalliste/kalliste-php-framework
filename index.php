<?php

require_once("config.php");

require_once("includes/general/request.php");

require_once("includes/users/user.php");
require_once("includes/users/admin.php");
require_once("includes/users/superadmin.php");
require_once("includes/users/session.php");
require_once("includes/users/login.php");

require_once("includes/models/projects.php");
require_once("includes/models/project_types.php");
require_once("includes/models/subjects.php");
require_once("includes/models/writer_project_types.php");
require_once("includes/models/writer_subjects.php");

require_once("includes/controllers/admin.php");
require_once("includes/controllers/superadmin.php");


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
  if (!logged_in() && !guest_allowed($action)) {
    app_redirect('login_page');
  }
  elseif ( ( admin_required($action) && !is_admin() && !is_superadmin() )
           or 
           ( superadmin_required($action) && !is_superadmin() )
         ) {
    app_redirect('forbidden_page'); 
  }
  $return = execute_action($action);
  if (is_array($return)) {
    print json_encode($return);
  }
  else {
    print $return;
  }
}


session_name("writers");
session_start();
refresh_session();
app_flow_control();

  
?>
