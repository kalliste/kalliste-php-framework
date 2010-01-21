<?php


function smarty_block_tabbed_box_tabs($params, $content, &$smarty) {
  if (empty($content)) {
    return;
  }
  else {
    if (array_key_exists('context', $params)) {
      foreach ($params['context'] as $k => $v) {
       if ($i) {
         $trail .= ' &gt;&gt; ';
       }
       if ($k != '') {
         $trail .= '<a href="'.$k.'">'.$v.'</a>';
       }
       else {
         $trail .= $v;
       }
       $i++;
      }
      if ($trail != '') {
        $trail = '<table class="menu"><tr><td>'.$trail.'</td></tr></table>';
      }
    }
    return '<table class="menu"><tr><td class="space">&nbsp;</td>'.$content.'<td class="endspace"></td></tr></table>'.$trail;
  }
}

?>
