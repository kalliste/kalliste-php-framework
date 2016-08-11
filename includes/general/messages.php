<?php


function add_message($message, $bin = 'default') {
    _messages(true, $message, $bin);
}

function dump_messages($bin = 'default') {
    $messages = _messages();
    if (array_key_exists($bin, $messages)) {
        return $messages[$bin];
    }
    return false;
}


function all_messages() {
    $messages = _messages();
    $out = array();
    foreach ($messages as $bin) {
        $out = array_merge($out, $bin);
    }
    return $out;
}


function _messages($adding = false, $add = '', $bin = 'default') {
    static $messages = array('default' => array());
    if ($adding) {
        $messages[$bin][] = $add;
    }
    return $messages;
}


?>
