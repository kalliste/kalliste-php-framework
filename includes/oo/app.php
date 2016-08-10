<?php

/*
Copyright (c) 2010, 2016 Kalliste Consulting, LLC

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


require_once("includes/oo/retro.php");
require_once("includes/oo/controller.php");
require_once("includes/general/request.php");
require_once("includes/general/template.php");


class kApp {

    public function preinit() {
        require_once("vendor/autoload.php");
        require_once("includes/oo/loader.php");
        spl_autoload_register('kloader');
    }


    public function load($path = "includes/controllers/") {
        $controllers = scandir($path);
        foreach ($controllers as $controller) {
            $parts = explode('.', $controller);
            if (end($parts) == 'php') {
                require_once($path.$controller);
                $class = reset($parts);
                new $class();
            }
        }
    }


    public function action_allowed($action) {
        return true;
    }


    public function index() {
        return '';
    }


    public function forbidden() {
        return '';
    }


    public function set_template_callbacks() {
        template_callbacks(array($this, 'debug_messages'), 'debug_messages');
    }


    public function debug_messages() {
        require_once("includes/general/messages.php");
        $show_queries = config('show_queries');
        $messages = array();
        //$show_queries = (!$show_queries && logged_in() && current_user_id() == 1) ? 1 : $show_queries;
        if ($show_queries) {
            $messages = dump_messages("sql_queries");
        }
        return $messages; 
    }


    public function called_action() {
        $action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';
        if ($action == "") {
            $action = "index";
        }
        elseif (!in_array($action, available_actions())) {
            //dbg($action, "action does not exist");
            $action = "index";
        }
        $allowed = $this->action_allowed($action);
        if (!$allowed) {
            $action = 'forbidden';
        }
        return $action;
    }

    public function run_action($action) {
        return execute_action($action);
    }

    public function run() {
        $this->preinit();
        $this->load();
        $this->set_template_callbacks();
        $action = $this->called_action();
        foreach (array('index', 'forbidden') as $def) {
            if ($action == $def && !in_array($action, available_actions())) {
                register_action($def, array($this, $def));
            }
        }
        $this->print_page($this->run_action($action), $action);
    }


    public function print_page($content, $action = '') {
        if (is_array($content)) {
            if (array_key_exists('encode', $content)) {
                switch($content['encode']) {
                case 'json':
                    $content = json_encode($content);
                    break;
                }
            }
        }
        if (is_array($content)) {
            $content = template_fill($action, $content);
        }
        print $content;
    }

}

?>
