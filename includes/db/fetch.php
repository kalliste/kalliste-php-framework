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

require_once("includes/db/generated.php");
require_once("includes/db/structures.php");

/***
 db/fetch.php
 Retrieve datbase records using a simple syntax
***/


//for example,   $user = get_assoc('users', compact('user_id'));
function get_assoc($table, $row_or_conditions, $field = "id") {
  if (is_array($row_or_conditions)) {
    return query_to_assoc("SELECT * FROM ".$table.array_to_where($row_or_conditions));
  }
  else {
    return query_to_assoc("SELECT * FROM ".$table." WHERE ".$field." = '".$row_or_conditions."'");
  }
}

function get_records($table, $conditions = array(), $paging_params = false) {
  $query = "SELECT * FROM ".$table.array_to_where($conditions);
  if ($paging_params) {
    $query .= sql_orders_limits($paging_params);
  }
  return query_to_assoc_list($query);
}

function get_value($table, $column, $conditions = array()) {
  return query_to_value("SELECT ".$column." FROM ".$table.array_to_where($conditions));
}

function get_list($table, $column, $conditions = array()) {
  return query_to_list("SELECT ".$column." FROM ".$table.array_to_where($conditions));
}

function get_hash($table, $key_column, $value_column, $conditions = array()) {
  return query_to_hash("SELECT ".$key_column.", ".$value_column." FROM ".$table.array_to_where($conditions));
}

?>
