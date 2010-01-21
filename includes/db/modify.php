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

require_once("includes/general/array.php");
require_once("includes/db/base.php");
require_once("includes/db/generated.php");

/***
 db/modify.php
 Create and modify database records
***/


function sql_transaction($lines = array(), $return_last_insert_id = TRUE) {
  sql_rollback();
  if (!is_array($lines)) {
    $lines = array($lines);
  }
  foreach ($lines as $line) {
    sql_query_dbg($line, true);
  }
  if (count($lines)) {
    if ($return_last_insert_id) {
      //we must get the insert id before commiting
      $ret = sql_insert_id();
    }
  }
  sql_commit();
  if (count($lines) && !$return_last_insert_id) {
    $ret = sql_affected_rows();
  }
  return $ret;
}


function generate_delete_sql($table, $conditions) {
  if (!is_array($conditions)) { dbg("must supply delete condition"); return FALSE; }
  return "DELETE FROM ".$table.array_to_where($conditions);
}


//each record must have the same fields in the same order for this function to work
function generate_insert_sql($table, $conditions, $records) {
  if (!is_array($conditions)) { $conditions = array(); }
  if (!is_array($records)) { $records = array(); }  
  if (count($records) > 0) {
    $first = reset($records);
    if (!is_array($first)) { return FALSE; }
    $keys = array_keys(array_merge($first, $conditions));
    $columns = implode(", ", $keys);
    $line = sprintf("INSERT INTO %s (%s) VALUES ", $table, $columns);
    $i = 0;
    foreach ($records as $record) {
      $i++;
      $record = array_merge($record, $conditions);
      if ($i > 1) { $line .= ","; }
      $line .= "('".implode("', '", array_escape($record))."')";
    }
    return $line;    
  }
  return FALSE;
}


//blob = array('column' => 'data')
function update_blob($table, $conditions, $blob) {
  if (!is_array($conditions)) { dbg("must supply update conditions"); return FALSE; }
  if (!is_array($blob)) { return FALSE; }
  $parts = str_split(reset($blob), 8192);
  $column = key($blob);
  $query = "UPDATE ".$table." SET ".$column."= ?".array_to_where($conditions);
  $db = getdb();
  $stmt = mysqli_prepare($db, $query);
  $empty = NULL;
  mysqli_stmt_bind_param($stmt, "b", $empty);
  foreach ($parts as $part) {
    mysqli_stmt_send_long_data($stmt, 0, $part);
  }
  mysqli_stmt_execute($stmt);
  sql_dbg($query);
  $affected = mysqli_stmt_affected_rows($stmt);
  mysqli_stmt_close($stmt);
  sql_commit();
  return $affected;  
}


function update_records($table, $conditions, $newdata) {
  if (!is_array($conditions)) { dbg("must supply update conditions"); return FALSE; }
  $newdata = array_escape($newdata);
  array_walk($newdata, 'key_equals_value');
  if (count($newdata) > 0) { 
    $query = "UPDATE ".$table." SET ".implode(", ", $newdata).array_to_where($conditions);
    sql_query_dbg($query);
    sql_commit();
    return sql_affected_rows();
  }
  return FALSE;
}


function insert_records($table, $conditions, $records) {
  if (!is_array($conditions)) { $conditions = array(); }
  if (is_array($records)) {
    if ($lines = generate_insert_sql($table, $conditions, $records)) {
      $insert_id = sql_transaction($lines);
      return $insert_id;
    }
  }
  dbg_r(compact('table', 'conditions', 'records'),"INSERT RECORDS NOT A NESTED ARRAY");
  return FALSE;
}


function adv_replace_records($table, $delete_conditions, $insert_conditions, $records) {
  if (!is_array($delete_conditions)) { dbg("must supply delete conditions"); return FALSE; }
  if (!is_array($insert_conditions)) { $insert_conditions = array(); }
  $queries[] = generate_delete_sql($table, $delete_conditions);
  $queries[] =  generate_insert_sql($table, $insert_conditions, $records);
  return sql_transaction($queries);
}


function delete_records($table, $conditions) {
  if (!is_array($conditions)) { dbg("must supply delete conditions"); return FALSE; }
  if (is_array(reset($conditions))) { dbg("conditions cannot be a nested array"); return FALSE; }
  $queries[] = generate_delete_sql($table, $conditions);
  return sql_transaction($queries);
}


function replace_records($table, $conditions, $records) {
  if (!is_array($conditions)) { dbg("must supply delete conditions"); return FALSE; }
  if (is_array(reset($conditions))) { dbg("conditions cannot be a nested array"); return FALSE; }
  $queries[] = generate_delete_sql($table, $conditions);
  $queries[] =  generate_insert_sql($table, $conditions, $records);
  return sql_transaction($queries);
}


?>
