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


function template_callbacks($callback = '', $var_name = '') {
  static $callbacks = array();
  if ($callback != '') {
    if ($var_name == '') {
      $var_name = $callback;
    }
    $callbacks[$var_name] = $callback;
  }
  return $callbacks;
}


function template_fill($file_name, $vars = array()) {
  require_once('includes/smarty/Smarty.class.php');
  if (substr($file_name, -4, 1) != '.') { $file_name .= '.tpl'; }
  $smarty = new Smarty();
  $base_dir = ''; $template = 'templates'; $compile = 'templates_c'; $cache = 'cache'; $config = 'configs';
  if (function_exists('template_config')) {
    extract(template_config());
  }
  $smarty->template_dir = $base_dir . $template;
  $smarty->compile_dir = $base_dir . $compile;
  $smarty->cache_dir = $base_dir . $cache;
  $smarty->config_dir = $base_dir . $config;
  foreach (template_callbacks() as $key => $callback) {
    $smarty->assign($key, call_user_func($callback));
  } 
  foreach ($vars as $key => $val) {
    $smarty->assign($key, $val);
  }
  return $smarty->fetch($file_name);
}


?>
