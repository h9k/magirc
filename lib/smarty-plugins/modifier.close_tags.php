<?php
/* $Id$
* Smarty plugin
* -------------------------------------------------------------
* Type:    modifier
* Name:    Close Tags Pro
* Version:    0.1
* Date:    2008-01-03
* Author:    Axel Kimmel, http://www.smarty-blog.de
* Purpose: close open tags
* Usage:    In the template, use
            {$string|CloseTags}
* Install: Drop into the plugin directory
* -------------------------------------------------------------
*/

function smarty_modifier_close_tags($html)
{
  $single = array('base', 'meta', 'link', 'hr', 'br', 'param', 'img', 'area', 'input', 'col', 'frame');
  preg_match_all("#<([a-z]+.*)>#iU",$html,$result);
  $openedtags=$result[1];

	$openedtags_new = array();
	if (is_array($openedtags))
		foreach ($openedtags as $key => $value) {
			if (substr($value, -1) == '/' || in_array(strtolower($value),$single)) {}
			else if (strpos($value,' ')!==false)
			{
				$parts =  explode(' ', $value);
				$openedtags_new[] = trim($parts[0]);
			}
			else $openedtags_new[] = $value;
		}
  preg_match_all("#</([a-z]+)>#iU",$html,$result);
  $closedtags=$result[1];
  $len_opened = count($openedtags_new);
  if(count($closedtags) == $len_opened) return $html;

  $openedtags = array_reverse($openedtags_new);
  for($i=0;$i < $len_opened;$i++) {
    if (!in_array($openedtags[$i],$closedtags) && !in_array(strtolower($openedtags[$i]),$single)){
      $html .= '</'.$openedtags[$i].'>';
    } else {
      if (array_search($openedtags[$i],$closedtags)!==false) unset($closedtags[array_search($openedtags[$i],$closedtags)]);
    }
  }
  return $html;
}

?>