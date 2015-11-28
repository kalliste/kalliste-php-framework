<?php

/*
Copyright (c) 2009 Kalliste Consulting, LLC

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/


require_once("includes/general/array.php");
require_once("includes/general/debug.php");
require_once("includes/general/template.php");


function action() {
  $args = func_get_args();
  $vars = array();
  if (count($args) > 0) {
    $action = array_shift($args);
    $vars = array_flatten($args);
    return register_action($action, $action, $vars);
  }
}


function register_action($action, $callback, $c_vars = array()) {
  return actions($action, compact('callback', 'c_vars'));
}


function available_actions() {
  return array_keys(actions('', '', TRUE));
}


function execute_action($action) {
  $myaction = actions($action);
  if (is_array($myaction)) { 
    extract($myaction);
    return call_user_func_array($callback, get_fields_ordered_numeric($_REQUEST, $c_vars));
  }
  return FALSE;
}


function actions($action, $append = '', $all = FALSE) {
  static $actions = array();
  if ($all) {
    return $actions;
  }
  if ($append == '') {
    if (isset($actions[$action])) {
      return $actions[$action];
    }
    return FALSE;
  }
  if ($action != '') {
    $actions[$action] = $append;
    return TRUE;
  }
  return FALSE;
}


function manual_redirect_page($url) {
  $capture_redirects = config('capture_redirects');
  return template_fill('manual_redirect.tpl', compact('url', 'capture_redirects'));
}


function app_redirect($action = "index", $vars = array()) {
  $vars['action'] = $action;
  if (config('capture_redirects')) {
    return manual_redirect_page('?'.http_build_query($vars));
  }
  else {
    header("Location: ?".http_build_query($vars));
    exit();
  }
}


function require_for_action($required = "", $action = "") {
  static $requirements = array();
  if ($action != "") {
    $requirements[$required][] = $action;
  }
  if ($required != "") {
    return $requirements[$required];
  }
  return $requirements;
}


function action_has_requirement($action, $requirement) {
  return in_array($action, require_for_action($requirement));
}


?>
