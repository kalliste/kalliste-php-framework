<?php

/*
Copyright (c) 2010, 2013 Kalliste Consulting, LLC

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
 Wrap the PDO database functions
***/
function getdb($dsn= '', $user = '', $password = '') {
  static $config;
  if ($dsn == '') {
    extract($config);
  }
  else {
    $config = compact('dsn', 'user', 'password');
  }
  static $db;
  if (!$db) {
    try {
      $db = new PDO($dsn, $user, $password);
      preg_match('/(.*):/', $dsn, $matches); // set database parameters based on dsn
      db_params('', $matches[1]);
    } catch (PDOException $e) {
      echo 'Connection failed: ' . $e->getMessage();
      exit();
    }
  }
  return $db;
}


function db_params($param, $set_config = '') {
  static $params;
  if ($set_config) {
    switch ($set_config) {
      case 'pgsql':
        $params = array('column_quote_char' => '"', 'column_quote_func' => 'key_equals_value', 'random' => 'RANDOM()');
				break;
			case 'sqlsrv':
        $params = array('column_quote_char' => ' ', 'column_quote_func' => 'key_equals_value', 'random' => 'RAND()');
				break;
			case 'mysql':
      default:
        $params = array('column_quote_char' => '`', 'column_quote_func' => 'backtick_key_equals_value', 'random' => 'RAND()');
        break;
    } 
  }
  return (array_key_exists($param, $params)) ? $params[$param] : '';
}


function sql_begin($db = NULL) {
  $db = ($db == NULL) ? getdb() : $db;
  return $db->beginTransaction();
}


function sql_commit($db = NULL) {
  $db = ($db == NULL) ? getdb() : $db;
  return $db->commit();
}


function sql_rollback($db = NULL) {
  $db = ($db == NULL) ? getdb() : $db;
  return $db->rollBack();
}


function sql_query($query) {
  $db = getdb();
  return $db->query($query);
}


function sql_query_dbg($query, $hide_errors = false) {
  $db = getdb();
  $return = $db->query($query);
  if (is_object($return)) {
    if ($return->errorCode() != '00000' && !$hide_errors) {
      list($sqlstate, $drivercode, $drivermessage) = $return->errorInfo();
      add_message("$query\n $sqlstate $drivercode $drivermessage", "sql_errors");
    }
  }
  else {
    if ($db->errorCode() != '00000' && !$hide_errors) {
      list($sqlstate, $drivercode, $drivermessage) = $db->errorInfo();
      add_message("$query\n $sqlstate $drivercode $drivermessage", "sql_errors");
    }
  }
  if (config('show_queries')) {
    add_message($query, "sql_queries");
  }
  return $return;
}


function sql_fetch_assoc($result) {
  if (is_object($result)) {
    return $result->fetch(PDO::FETCH_ASSOC);
  }
  return false;
}


function sql_fetch_row($result) {
  if (is_object($result)) {
    return $result->fetch(PDO::FETCH_NUM);
  }
  return false;
}


function sql_update_blob($table, $column, $conditions, $blob) {
  $db = getdb();
  $stmt = $db->prepare("UPDATE ".$table." SET `".$column."`= ?".sql_where($conditions));
  $stmt->bindParam(1, $blob, PDO::PARAM_LOB);
  $db->beginTransaction();
  $stmt->execute();
  $db->commit();
  return $stmt;
}


function sql_close($stmt) {
  if (is_object($stmt)) {
    $stmt->closeCursor();
  }
}


function sql_insert_id() {
  $db = getdb();
  return $db->lastInsertId();
}


function sql_affected_rows($result) {
  return (is_object($result)) ? $result->rowCount() : false;
}


function escape_str($str) {
  $db = getdb();
  return substr($db->quote($str), 1, -1);
}


function array_escape($arr) {
  if (!is_array($arr)) {
    return escape_str($arr);
  }
  return array_map('array_escape', $arr);
}

?>
