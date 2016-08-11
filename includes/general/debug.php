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


function dbg_enabled() {
    return config('show_debug');
}


function dbg($info, $label = "") {
    if ($label != "") {
        _dbginfo($label.":\n".$info);
    }
    else {
        _dbginfo($info);
    }
}


function dbg_export($var, $label = "") {
    $var = var_export($var, TRUE);
    dbg($var, $label);
}


function dbg_r($info, $label = "") {
    _dbginfo_r($info, $label);
}


function _dbginfo($info) {
    if (dbg_enabled()) {
        if (php_sapi_name() == 'cli') {
            print $info."\n";
        }
        else {
            print "<pre>".$info."</pre><br />\n";
        }
    }
}


function _dbginfo_r($info, $label) {
    if (dbg_enabled()) {
        if (php_sapi_name() == 'cli') {
            print "\n";
        }
        else {
            print "<pre>\n";
        }
        if ($label != "") {
            if (php_sapi_name() == 'cli') {
                print $label."\n";
            }
            else {
                print $label.":<br />\n";
            }
        }
        print_r($info);
        if (php_sapi_name() == 'cli') {
            print "\n";
        }
        else {
            print "</pre><br />\n";
        }
    }
}


?>
