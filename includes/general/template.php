<?php

/*
Copyright (c) 2009, 2016 Kalliste Consulting, LLC

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


function process_callbacks($callbacks) {
    $ret = array();
    foreach ($callbacks as $key => $callback) {
        $ret[$key] = call_user_func($callback);
    }
    return $ret;
}


function template_fill($template_name, $vars = array()) {
    $loader = new Twig_Loader_Filesystem('templates');
    $twig = new Twig_Environment($loader, array(
        'cache' => false,
        'autoescape' => true,
        'autoreload' => true,
    ));
    $class = new ReflectionClass($twig);
    $methods = $class->getMethods();
    $twig->addGlobal('get', $_GET);
    $twig->addGlobal('post', $_POST);
    $twig->addGlobal('request', $_REQUEST);
    $twig->addGlobal('session', $_SESSION);
    $callback_values = process_callbacks(template_callbacks());
    $vars = array_merge($callback_values, $vars);
    $template = $twig->loadTemplate($template_name.".html");
    return $template->render($vars);
}


?>
