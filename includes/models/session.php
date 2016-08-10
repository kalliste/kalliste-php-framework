<?php

/*
Copyright (c) 2016 Kalliste Consulting, LLC

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

class session {

    static function login($user) {
        session_unset();
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login_time'] = time();
        $_SESSION['refresh_time'] = time();
        $_SESSION['user_type'] = $user['type'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
    }

    static function logout() {
        session_unset();
    }

    static function logged_in() {
        if ((array_key_exists('email', $_SESSION)) && (time() - $_SESSION['refresh_time']) < (60 * 60 * 24)) {
            return true; 
        }
        return false;
    }

    static function refresh() {
        $_SESSION['refresh_time'] = time();
    }

    static function uid() {
        return $_SESSION['user_id'];
    }

    static function login_type() {
        return array_key_exists('user_type', $_SESSION) ? $_SESSION['user_type'] : '';
    }

}

?>
