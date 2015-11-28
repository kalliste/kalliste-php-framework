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

require_once("includes/db/base.php");

/***
 db/structures.php
 Return structured data from queries
***/

//returns a one dimensional array with the first column of every row from a query
function query_to_list($query) {
  $ret = array();
  $result = sql_query_dbg($query);
  while ($row = sql_fetch_row($result)) {
    $ret[] = $row[0];
  }
  return $ret;
}


function query_to_assoc($query) {
  $result = sql_query_dbg($query);
  if ($row = sql_fetch_assoc($result)) {
    sql_close($result);
    return $row;
  }
  return FALSE;
}


function query_to_value($query) {
  $result = sql_query_dbg($query);
  if ($row = sql_fetch_row($result)) {
    sql_close($result);
    return $row[0];
  }
  return FALSE;
}


function query_to_assoc_list($query) {
  $ret = array();
  $result = sql_query_dbg($query);
  if (!$result) { return FALSE; }
  while ($row = sql_fetch_assoc($result)) {
    $ret[] = $row;
  }
  return $ret;
}


function query_to_hash($query) {
  $ret = array();
  $result = sql_query_dbg($query);
  while ($row = sql_fetch_row($result)) {
    $ret[$row[0]] = $row[1];
  }
  return $ret;
}

?>
