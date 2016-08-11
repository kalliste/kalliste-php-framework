<?php

/*
Copyright (c) 2010 Kalliste Consulting, LLC

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

require_once("includes/general/request.php");
require_once("includes/oo/retro.php");

abstract class kController {

    function __construct() {
        $class = get_called_class();
        $methods = get_class_methods($class);
        foreach ($methods as $method) {
            if ($method{0} != '_') {
                $rmethod = new ReflectionMethod($class, $method);
                $rparams = $rmethod->getParameters();
                $params = array();
                foreach ($rparams as $param) {
                    $params[] = $param->getName();
                }
                register_action($method, array($this, $method), $params);
            }
        }    
    }

}

?>
