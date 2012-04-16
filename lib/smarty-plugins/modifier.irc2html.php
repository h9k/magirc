<?php


/*
 * converts irc text with control codes to xhtml, used by magirc
 */

function translatecolorcode($matches) {
	$colors = array(
		'#FFFFFF',
		'#000000',
		'#00007F',
		'#009300',
		'#FF0000',
		'#7F0000',
		'#9C009C',
		'#FC7F00',
		'#FFFF00',
		'#00FC00',
		'#009393',
		'#00FFFF',
		'#0000FC',
		'#FF00FF',
		'#7F7F7F',
		'#D2D2D2'
		);
		$options = '';

		if($matches[2] != '') {
			$bgcolor = trim(substr($matches[2],1));
			if ((int)$bgcolor < count($colors)) {
				$options .= 'background-color: ' . $colors[(int)$bgcolor] . '; ';
			}
		}

		$forecolor = trim($matches[1]);
		if($forecolor != '' && (int)$forecolor < count($colors)) {
			$options .= 'color: ' . $colors[(int)$forecolor] . ';';
		}

		if($options != '') {
			return '<span style="' . $options . '">' . $matches[3] . '</span>';
		} else {
			return $matches[3];
		}
}

function smarty_modifier_irc2html($text) {
	$lines = explode("\n", utf8_decode($text));
	$out = '';

	foreach($lines as $line) {
		$line = nl2br(htmlentities($line,ENT_COMPAT,'UTF-8'));
		// replace control codes
		$line = preg_replace_callback('/[\003](\d{0,2})(,\d{1,2})?([^\003\x0F]*)(?:[\003](?!\d))?/','translatecolorcode',$line);
		$line = preg_replace('/[\002]([^\002\x0F]*)(?:[\002])?/','<strong>$1</strong>',$line);
		$line = preg_replace('/[\x1F]([^\x1F\x0F]*)(?:[\x1F])?/','<span style="text-decoration: underline;">$1</span>',$line);
		$line = preg_replace('/[\x12]([^\x12\x0F]*)(?:[\x12])?/','<span style="text-decoration: line-through;">$1</span>',$line);
		$line = preg_replace('/[\x16]([^\x16\x0F]*)(?:[\x16])?/','<span style="font-style: italic;">$1</span>',$line);
		$line = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\S+]*(\?\S+)?)?)?)@', "<a href='$1' class='topic'>$1</a>", $line);
		// remove dirt
		$line = preg_replace('/[\x00-\x1F]/', '', $line);
		$line = preg_replace('/[\x7F-\xFF]/', '', $line);
		// append line
		if($line != '') {
			$out .= $line;
		}
	}

	return $out;
}

?>