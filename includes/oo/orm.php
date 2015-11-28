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

require_once("includes/db/structures.php");
require_once("includes/db/modify.php");

abstract class kORM {

  /* Fetch functions */

  public static function assoc($conditions = array(), $field = "id") {
    $conditions = (is_array($conditions)) ? $conditions : array($field => $conditions);
    return query_to_assoc("SELECT * FROM ".get_called_class().sql_where($conditions));
  }

  public static function records($conditions = array(), $paging_params = false) {
    $query = "SELECT * FROM ".get_called_class().sql_where($conditions);
    if ($paging_params) {
      $query .= sql_orders_limits($paging_params);
    }
    return query_to_assoc_list($query);
  }

  public static function value($column, $conditions = array()) {
    $colquote = db_params('column_quote_char');
    return query_to_value("SELECT $colquote".$column."$colquote FROM ".get_called_class().sql_where($conditions));
  }

  public static function values($column, $conditions = array()) {
    $colquote = db_params('column_quote_char');
    return query_to_list("SELECT $colquote".$column."$colquote FROM ".get_called_class().sql_where($conditions));
  }

  public static function hash($key_column, $value_column, $conditions = array(), $paging_params = false) {
    $colquote = db_params('column_quote_char');
    $query = "SELECT $colquote".$key_column."$colquote, $colquote".$value_column."$colquote FROM ".get_called_class().sql_where($conditions);
    if ($paging_params) {
      $query .= sql_orders_limits($paging_params);
    }
    return query_to_hash($query);
  }
  
  /* Modify functions */

  public static function blob($column, $conditions, $blob) {
    if (!is_array($conditions)) {
      $conditions = array('id' => $conditions);
    }
    return update_blob(get_called_class(), $column, $conditions, $blob);
  }
  
  public static function update($conditions, $newdata) {
    if (!is_array($conditions)) {
      $conditions = array('id' => $conditions);
    }
    return update_records(get_called_class(), $conditions, $newdata);
  }
  
  public static function insert($x, $y = '') {
    $class = get_called_class();
    if (is_array($y)) {
      $conditions = $x;
      $records = $y;
    } 
    else {
      $conditions = array();
      if (is_array(reset($x))) {
        $records = $x;
      }
      else {
        $records = array($x);
      }
    }
    if (method_exists($class, 'prepare_record')) {
      foreach ($records as $k => &$record) {
        call_user_func_array(array($class, 'prepare_record'), array($conditions, &$record, 'insert'));
      }
    }
    return insert_records($class, $conditions, $records);
  }
  
  public static function delete($conditions = array()) {
    if (!is_array($conditions)) {
      $conditions = array('id' => $conditions);
    }
    return delete_records(get_called_class(), $conditions);
  }
  
  public static function replace($conditions, $records) {
    return replace_records(get_called_class(), $conditions, $records);
  }

}

?>
