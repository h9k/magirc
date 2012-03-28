<?php

if (isset($_POST['form'])) {
	if (isset($_POST['debug_mode'])) $admin->saveConfig('debug_mode',$_POST['debug_mode']);
	$admin->tpl->assign('success', true);
}

$admin->tpl->display('advanced.tpl');
?>