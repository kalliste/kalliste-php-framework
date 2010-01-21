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

require_once("includes/general/debug.php");
require_once("includes/general/messages.php");

/***
 db/base.php
 Wrap the vendor-specific database functions
***/

function getdb($server = '', $user = '', $password = '', $database = '') {
  static $db;
  if (!$db) {
    $db =& sql_connect($server, $user, $password, $database);
     sql_rollback($db);
     sql_autocommit(FALSE, $db);
  }
  if (!$db) {
    die ("Couldn't connect to MySQL server");
  }
  return $db;
}


function sql_connect($server = '', $user = '', $password = '', $database = '') {
  return mysqli_connect($server, $user, $password, $database);
}


function sql_commit() {
  $db = getdb();
  mysqli_commit($db);
}


function sql_rollback($db = NULL) {
  if ($db == NULL) {
    $db = getdb();
  }
  mysqli_rollback($db);
}


function sql_autocommit($bool, $db = NULL) {
  if ($db == NULL) {
    $db = getdb();
  }
  mysqli_autocommit($db, $bool);
}


function sql_query($query) {
  $db = getdb();
  return mysqli_query($db, $query);
}


function sql_query_dbg($query, $hide_errors = false) {
  $return =& sql_query($query);
  sql_dbg($query, $hide_errors);
  return $return;
}


//run directly after performing the query
function sql_dbg($query = "", $hide_errors = false) {
  $db = getdb();
  $str = mysqli_error($db);
  if ($str != "" && !$hide_errors) {
    add_message($query."\n".$str, "sql_errors");
  }
  if (config('show_queries')) {
    add_message($query, "sql_queries");
  }
}


function sql_fetch_array(&$result) {
  return mysqli_fetch_array($result);
}


function sql_fetch_assoc(&$result) {
  return mysqli_fetch_assoc($result);
}


function sql_fetch_row(&$result) {
  return mysqli_fetch_row($result);
}


function sql_insert_id() {
  $db = getdb();
  return mysqli_insert_id($db);
}


function sql_num_rows(&$result) {
 return mysqli_num_rows($result);
}


function sql_affected_rows() {
  $db = getdb();
  return mysqli_affected_rows($db);
}


function escape_str($str) {
  $db = getdb();
  $ret = mysqli_real_escape_string($db, $str);
  return $ret;
}


function array_escape($arr) {
  if (!is_array($arr)) {
    $arr = array($arr);
  }
  return array_map('escape_str', $arr);
}

?>
