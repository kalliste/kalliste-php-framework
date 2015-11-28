<?php

/*
Copyright (c) 2009, 2012 Kalliste Consulting, LLC

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

/***
 db/generated.php
 Generate database query parts
***/


function column_key_equals_value(&$item, $key, $params) {
  $item = ($params['table'] != '') ? $params['colquote'].$params['table'].$params['colquote'].".".$params['colquote'].$key.$params['colquote']."='".$item."'" 
                                   : $params['colquote'].$key.$params['colquote']."='".$item."'";
}


function sql_where($conditions = array(), $table = '') {
  if (is_array($conditions)) {
    if (count($conditions) > 0) {
      array_walk($conditions, 'column_key_equals_value', array('table' => $table, 'colquote' => db_params('column_quote_char')));
      return " WHERE (1=1) AND (".implode(") AND (", $conditions).")";
    }
    return " WHERE (1=1) ";
  }
  if (stripos($conditions, 'WHERE') === false) {
    return " WHERE ".$conditions." ";
  }
  return " ".$conditions." ";
}


/*
* Example Input:
* ("key", array('foo', 'bar', baz'))
* Output:
* array(array('key' => 'foo'), array('key' => 'bar'), array('key' => 'baz'))
* useful when turning an array of values into INSERTS using insert_records()
*/
function prepare_form_multivalues($key, $values) {
  if (count($values)) {
    $names = array_fill(1, count($values), $key);
    return array_map("keyify", $names, $values);
  }
  return FALSE;
}


function paging_default_vars() {
  return array('sort', 'sort2', 'sort3', 'order', 'order2', 'order3', 'page', 'per_page');
}


function array_filter_rekey($vars, $defaults, $transforms = array()) {
  if (empty($transforms)) { return get_fields($vars, $defaults); }
  $defaults = array_combine(array_flip($defaults), $defaults);
  $key_name_mapping = get_fields(array_merge($defaults, $transform), $defaults);
  return array_combine(array_keys($key_name_mapping), get_fields_ordered_numeric($vars, $key_name_mapping));
}


function pull_query_params($vars, $key_names = array()) {
  return array_filter_rekey($vars, paging_default_vars(), $key_names);
}


function push_query_params($vars, $key_names = array()) {
  return array_filter_rekey($vars, paging_default_vars(), array_flip($key_names));
}


//get link paramaters for a list of columns
function column_sort_links($columns, $old_params, $merge = array()) {
  foreach ($columns as $column) {
    $links[$column] = http_build_query(not_empty(array_merge($merge, sort_query_vars($column, $old_params))));
  }
  return $links;
}


//used to generate links for column headers
function sort_query_vars($clicked_column, $old_params, $key_names = array()) {
  $sort = $sort2 = $sort3 = $order = $order2 = $order3 = $page = $per_page = '';
  extract(pull_query_params($old_params, $key_names));
  if ($clicked_column == $sort) {
    $order = (strtoupper($order) == 'DESC') ? 'ASC' : 'DESC'; 
  } else {
    $sort3 = $sort2;
    $order3 = $order2;
    $sort2 = $sort;
    $order2 = $order;
    $sort = $clicked_column;
    $order = 'ASC';
  }
  return push_query_params(compact(paging_default_vars()), $key_names);
}


function not_array($x) {
  return (!is_array($x));
}


// pass $_REQUEST to it and get string to add to query
function sql_orders_limits($params, $key_names = array()) {
  $params = preg_replace('/[^A-Za-z0-9_]/', '', array_filter($params, 'not_array'));
  $sort = $sort2 = $sort3 = $order = $order2 = $order3 = $page = $per_page = '';
  extract(pull_query_params($params, $key_names));
  return sql_order_str($sort, $order, $sort2, $order2, $sort3, $order3).sql_page_limit($page, $per_page);
}


function sql_order_str($col1 = '', $col1_order = '', $col2 = '', $col2_order = '', $col3 = '', $col3_order = '') {
  $colquote = db_params('column_quote_char');
  if ($col1 == '') { return ''; }
  $col1_order = (strtoupper($col1_order) == 'DESC') ? 'DESC' : '';
  $col2_order = (strtoupper($col2_order) == 'DESC') ? 'DESC' : '';
  $col3_order = (strtoupper($col3_order) == 'DESC') ? 'DESC' : '';
  $order_str = " ORDER BY $colquote".$col1."$colquote ".$col1_order;
  if ($col2 != "") { $order_str .= ", $colquote".$col2."$colquote ".$col2_order; }
  if ($col3 != "") { $order_str .= ", $colquote".$col3."$colquote ".$col3_order; }
  return $order_str.' ';
}


// Pages are numbered from 1
function sql_page_limit($page, $per_page) {
  settype($page, "integer");
  settype($per_page, "integer");
  if ($page < 1) { $page = 1; }
  if ($per_page < 1) { return ''; }
  return ' LIMIT '.($per_page * ($page - 1)).', '.$per_page.' ';
}

?>
