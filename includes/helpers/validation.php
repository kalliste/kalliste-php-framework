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



/*
 * Domain names must be between 3 and 63 characters
 * The parts are delimited by periods, and there must be at least 2 parts
 * The final part must be a valid Top Level Domain
 * For our purposes all TLDs are valid
 * No part may start or end with a dash
 * All parts may be composed only of numbers, letters, and dashes
 */
function domain_name_validate($name) {
    $name = strtolower($name);
    $length = strlen($name);
    if ($length < 3 || $length > 63) {
        return FALSE;
    }
    $parts = explode(".", $name);
    if (count($parts) < 2) { return FALSE; }
    foreach($parts as $part) {
        if (substr($part, 0, 1) == '-' || substr($part, -1, 1) == '-') {
            return FALSE;
        }
        if ($part != preg_replace('[^0-9a-f\-]', '', $part)) {
            return FALSE;
        }
    }
    return TRUE;
}


//for our purposes an e-mail address is any series of printable lower-ascii characters
//that is not in the list of specials, followed by an @, followed by a valid domain name
function email_validate($email) {
    $specials = array("(", ")", "<", ">", ",", ";", ":", "\\", "\"", "[", "]");
    $parts = explode("@", $email);
    if (count($parts) != 2) { return FALSE; }
    if (domain_name_validate($parts[1]) != TRUE) { return FALSE; }
    $local_part = $parts[0];
    $len = strlen($local_part);
    for ($i = 0; $i < $len; $i++) {
        if (in_array($local_part{$i}, $specials)) {
            return FALSE;
        }
        $ord = ord($local_part{$i});
        if ($ord < 33 || $ord > 126) {
            return FALSE;
        }
    }
    return TRUE;
}


//Suitable for validating phone numbers where there must be digits and may also be other characters
function min_digits_validate($str, $min_digits = 10) {
    for ($pos = 0; $pos < strlen($str); $pos++) {
        if (is_numeric(substr($str, $pos, 1))) {
            $digits++;
        }
    }
    if ($digits >= $min_digits) {
        return true;
    }
false;
}


function max_digits_validate($str, $max_digits = 10) {
    for ($pos = 0; $pos < strlen($str); $pos++) {
        if (is_numeric(substr($str, $pos, 1))) {
            $digits++;
        }
    }
    if ($digits <= $max_digits) {
        return true;
    }
false;
}


?>
