<?php


function smarty_block_tabbed_box($params, $content, &$smarty) {
  if (empty($content)) {
    return;
  }
  else {
    return '<table class="menuouter"><tr><td align="center" class="'.$params['tdclass'].'">'.$content.'</td></tr></table>';
  }
}

?>
