<?php

function smarty_function_tabbed_box_tab($params, &$smarty) {
  if ($params['selected'] == 'auto') {
    $class = 'unselected';
    parse_str(substr($params['link'], 1), $parts);
    if (count($parts)) {
      $class = 'selected';
      foreach ($parts as $k => $v) {
        if ($_GET[$k] != $v) {
          $class = 'unselected';
        }
      }
    }
  }
  else {
    $class = (array_key_exists('selected', $params)) ? 'selected' : 'unselected';
  }
  $txt  = '<td class="'.$class.'">';
  if (array_key_exists('link', $params) && !($params['selected'] == 'auto' && $class == 'selected')) {
    $txt .= '<a href="'.$params['link'].'">'.$params['text'].'</a>';
  }
  else {
    $txt .= '<span>'.$params['text'].'</span>';
  }
  $txt .= '</td><td class="space">&nbsp;</td>';
  return $txt;
}

?>
