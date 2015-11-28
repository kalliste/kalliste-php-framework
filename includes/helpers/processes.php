<?php

/*
Copyright (c) 2010 Kalliste Consulting, LLC

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


function run_in_background($command) {
  return (int) shell_exec("nohup ".$command." >/dev/null 2>&1 & echo $!");
}


function is_process_running($pid) {
  settype($pid, 'integer');
  exec("ps ".$pid, $state);
  return(count($state) >= 2);
}


function process_name($pid) {
  settype($pid, 'integer');
  exec("ps ".$pid." | tail -n 1 | cut -c 28-", $state);
  return reset($state);
}


function stop_process($pid) {
  settype($pid, 'integer');
  exec("kill ".$pid);
}


function hard_stop_process($pid) {
  settype($pid, 'integer');
  exec("kill -9 ".$pid);
}


?>
