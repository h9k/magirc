<?php
// $Id$

$file = isset($_GET['f']) ? stripslashes($_GET['f']) : 'README';
$path = '../doc/' . basename($file);
if (is_file($path)) {
	$text = file_get_contents($path);
	$admin->tpl->assign('text', htmlspecialchars($text));
} else {
	$admin->tpl->assign('text', "ERROR: Specified documentation file not found");
}

$admin->tpl->display('docs.tpl');
?>