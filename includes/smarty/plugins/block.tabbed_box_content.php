<?php


function smarty_block_tabbed_box_content($params, $content, &$smarty) {
  if (empty($content)) {
    return;
  }
  else {
    return '<table class="content"><tr><td>'.$content.'</td></tr></table>';
  }
}

?>
