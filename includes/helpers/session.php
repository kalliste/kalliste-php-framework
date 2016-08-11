<?php


function current_user_id() {
    return (array_key_exists('user_id', $_SESSION)) ? $_SESSION['user_id'] : false;
}


function logged_in() {
    if (array_key_exists('refresh_time', $_SESSION)) {
        if ($_SESSION['refresh_time'] > (time() - (2 * 7 * 24 * 60 * 60))) {
            return true;
        }
    }
    return false;
}


function session_var($var) {
    if (logged_in()) {
        if (array_key_exists($var, $_SESSION)) {
            return $_SESSION[$var];
        }
    }
    return false;
}


function login_type() {
    return session_var('user_type');
}


function login_time() {
    return session_var('login_time');
}


function is_user_type($user_type) {
    if ($user_type == login_type()) {
        return true;
    }
    return false;
}


function is_user() {
    return is_user_type('user');
}


function is_admin() {
    return is_user_type('admin');
}


function is_supervisor() {
    return is_user_type('supervisor');
}


function refresh_session() {
    if (logged_in()) {
        $time = time();
        $_SESSION['refresh_time'] = $time;
    }
}


?>
