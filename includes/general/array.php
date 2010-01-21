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


function array_keys_exist($keys, $array) {
  foreach ($keys as $key) {
    if (!array_key_exists($key, $array)) {
      return FALSE;
    }
  }
  return TRUE;
}


function keyify($key, $value) {
  return array($key => $value);
}


function get_fields($array, $fields) {
  if (!is_array($array)) { return FALSE; }
  return array_intersect_key($array, array_flip($fields));
}


function key_by_name($array) {
  $ret = array();
  foreach ($array as $val) {
    $ret[$val] = $val;
  }
  return $ret;
}


function get_fields_ordered_numeric($array, $fields) {
  $ret = array();
  foreach ($fields as $field) {
    $ret[] = $array[$field];
  }
  return $ret;
}


function array_flatten($arr) {
  $ret = array();
  foreach ($arr as $el) {
    if (is_array($el)) {
      $ret = array_merge($ret, array_flatten($el));
    }
    else {
      $ret[] = $el;
    }
  }
  return $ret;
}


function array_numeric_nokeys($arr) {
  $out = array();
  if (is_array($arr)) {
    foreach ($arr as $value) {
      settype($value, "integer");
      $out[] = $value;
    }
  }
  return $out;
}


function over_zero($array) {
  $ret = array();
  foreach ($array as $key => $value) {
    if ($value > 0) {
      $ret[$key] = $value;
    }
  }
  return $ret;
}


function not_empty($array) {
  $ret = array();
  foreach ($array as $key => $value) {
    if ($value != '' || $value > 0) {
      $ret[$key] = $value;
    }
  }
  return $ret;
}


function key_equals_value(&$item, $key) {
  $item = $key."='".$item."'";
}


function key_colon_value(&$item, $key) {
  $item = $key.": ".$item."";
}

?>
