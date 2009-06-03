<?php
// $Id: modifier.CloseTags.php 111 2009-04-24 13:26:39Z hosting9000.com $

/*
 * converts irc color codes to html, used by magirc
 */

function smarty_modifier_irc2html($text) {
	$color["color:0;"] = "color:#FFFFFF;";
	$color["color:1;"] = "color:#000000;";
	$color["color:2;"] = "color:#00007F;";
	$color["color:3;"] = "color:#009300;";
	$color["color:4;"] = "color:#FF0000;";
	$color["color:5;"] = "color:#7F0000;";
	$color["color:6;"] = "color:#9C009C;";
	$color["color:7;"] = "color:#FC7F00;";
	$color["color:8;"] = "color:#FFFF00;";
	$color["color:9;"] = "color:#00FC00;";
	$color["color:10;"]= "color:#009393;";
	$color["color:11;"]= "color:#00FFFF;";
	$color["color:12;"]= "color:#0000FC;";
	$color["color:13;"]= "color:#FF00FF;";
	$color["color:14;"]= "color:#7F7F7F;";
	$color["color:15;"]= "color:#D2D2D2;";
	
	/* Wrap the text to avoid overflowing the layout */
	/* If the second parameter of this function is 0, wrapping will be skipped */
	$numargs = func_num_args();
	$arg_list = func_get_args();
	$wrap = ($numargs > 1) ? $arg_list[1] : 1;
	if ($wrap == 1) {
		$text = str_replace(chr(03), ' ' . chr(03), $text);
		$text = preg_replace('#([^\n\r ]{60})#i', '\\1  ', $text);
	}
	
	/* Transform the text into xhtml */
	$text = @htmlentities($text,ENT_COMPAT,'UTF-8');
	$text = nl2br($text);
	
	$ctrl->k = chr(03);
	while(ereg("$ctrl->k([0-9]{1,2}),([0-9]{1,2})([^$ctrl->k]*)$ctrl->k([:alpha:])",$text))
	{
		$text = ereg_replace("$ctrl->k([0-9]{1,2}),([0-9]{1,2})([^$ctrl->k]*)$ctrl->k","<span style=\"color:\\1; background-color:\\2;\">\\3</span>",$text);
	}
	while(ereg("$ctrl->k([0-9]{1,2}),([0-9]{1,2})(.*)",$text))
	{
		$text = ereg_replace("$ctrl->k([0-9]{1,2}),([0-9]{1,2})(.*)","<span style=\"color:\\1; background-color:\\2;\">\\3</span>",$text);
	}
	while(ereg("$ctrl->k([0-9]{1,2})([^$ctrl->k]*)$ctrl->k([0-9]{1,2})",$text))
	{
		$text = ereg_replace("$ctrl->k([0-9]{1,2})([^$ctrl->k]*)$ctrl->k([0-9]{1,2})","<span style=\"color:\\1;\">\\2</span>$ctrl->k\\3",$text);
	}
	while(ereg("$ctrl->k([0-9]{1,2})([^$ctrl->k]*)$ctrl->k",$text))
	{
		$text = ereg_replace("$ctrl->k([0-9]{1,2})([^$ctrl->k]*)$ctrl->k","<span style=\"color:\\1;\">\\2</span>",$text);
	}
	while(ereg("$ctrl->k([0-9]{1,2})(.*)",$text))
	{
		$text = ereg_replace("$ctrl->k([0-9]{1,2})(.*)","<span style=\"color:\\1;\">\\2</span>",$text);
	}
	$text = strtr($text,$color);
	
	$ctrl_b = chr(02);
	$text = ereg_replace("$ctrl_b([^\r$ctrl_b]*)[$ctrl_b]","<span style=\"font-weight: bold;\">\\1</span>",$text);
	$text = ereg_replace("$ctrl_b([^\r$ctrl_b]*)","<span style=\"font-weight: bold;\">\\1</span>\r",$text);
	$ctrl_u = chr(31);
	$text = ereg_replace("$ctrl_u([^\r$ctrl_u]*)[$ctrl_u]","<span style=\"text-decoration: underline;\">\\1</span>",$text);
	$text = ereg_replace("$ctrl_u([^\r$ctrl_u]*)","<span style=\"text-decoration: underline;\">\\1</span>\r",$text);
	$ctrl_i = chr(22);
	$text = ereg_replace("$ctrl_i([^\r$ctrl_i]*)[$ctrl_i]","<span style=\"font-style: italic;\">\\1</span>",$text);
	$text = ereg_replace("$ctrl_i([^\r$ctrl_i]*)","<span style=\"font-style: italic;\">\\1</span>\r",$text);
	$ctrl_s = chr(18);
	$text = ereg_replace("$ctrl_s([^\r$ctrl_s]*)[$ctrl_s]","<span style=\"text-decoration: line-through;\">\\1</span>",$text);
	$text = ereg_replace("$ctrl_s([^\r$ctrl_s]*)","<span style=\"text-decoration: line-through;\">\\1</span>\r",$text);
	for ($i = 0; $i < 32; $i++)	{ $text = str_replace(chr($i), "", $text); }
	for ($i = 127; $i < 256; $i++)	{ $text = str_replace(chr($i), "&nbsp;", $text); }
	
	$text = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\" class=\"topic\">\\0</a>", $text);
	$text = ereg_replace("(^| )(www([-]*[.]?[^<>[:space:]]+[[:alnum:]/])*)","\\1<a href=\"http://\\2\" class=\"topic\">\\2</a>", $text);
	
	return $text;
}

?>