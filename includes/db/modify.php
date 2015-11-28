<?php

/*
Copyright (c) 2009, 2013 Kalliste Consulting, LLC

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
  //sql_rollback();
  sql_begin();
  if (!is_array($lines)) {
    $lines = array($lines);
  }
  foreach ($lines as $line) {
    $result = sql_query_dbg($line, true);
  }
  if (count($lines)) {
    if ($return_last_insert_id) {
      //we must get the insert id before commiting
      $ret = sql_insert_id();
    }
  }
  sql_commit();
  if (count($lines) && !$return_last_insert_id) {
    $ret = sql_affected_rows($result);
  }
  return $ret;
}


function generate_delete_sql($table, $conditions) {
  return "DELETE FROM ".$table.sql_where($conditions);
}


//each record must have the same fields in the same order for this function to work
function generate_insert_sql($table, $conditions, $records) {
  $colquote = db_params('column_quote_char');
  if (!is_array($conditions)) { $conditions = array(); }
  if (!is_array($records)) { $records = array(); }  
  if (count($records) > 0) {
    $first = reset($records);
    if (!is_array($first)) { return FALSE; }
    $keys = array_keys(array_merge($first, $conditions));
    $columns = $colquote.implode("$colquote, $colquote", $keys).$colquote;
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
function update_blob($table, $column, $conditions, $blob) {
  $result = sql_update_blob($table, $column, $conditions, $blob);
  return sql_affected_rows($result);
}


function update_records($table, $conditions, $newdata) {
  $newdata = array_escape($newdata);
  $column_quote_func = db_params('column_quote_func');
  array_walk($newdata, $column_quote_func);
  if (count($newdata) > 0) { 
    $query = "UPDATE ".$table." SET ".implode(", ", $newdata).sql_where($conditions);
    $result = sql_query_dbg($query);
    return sql_affected_rows($result);
  }
  return FALSE;
}


function insert_records($table, $conditions, $records) {
  if (is_array($records)) {
    if ($lines = generate_insert_sql($table, $conditions, $records)) {
      $insert_id = sql_transaction($lines);
      return $insert_id;
    }
  }
  dbg_r(compact('table', 'conditions', 'records'), "INSERT RECORDS NOT A NESTED ARRAY");
  return FALSE;
}


function adv_replace_records($table, $delete_conditions, $insert_conditions, $records) {
  $queries[] = generate_delete_sql($table, $delete_conditions);
  $queries[] =  generate_insert_sql($table, $insert_conditions, $records);
  return sql_transaction($queries);
}


//fixme - detect nesting
function delete_records($table, $conditions) {
  $queries[] = generate_delete_sql($table, $conditions);
  return sql_transaction($queries);
}


function replace_records($table, $conditions, $records) {
  $queries[] = generate_delete_sql($table, $conditions);
  $queries[] =  generate_insert_sql($table, $conditions, $records);
  return sql_transaction($queries);
}


?>
