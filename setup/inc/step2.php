<?php

$check = $setup->configCheck();
$setup->tpl->assign('check', $check);
if (!$check) { // Dump file to db
	$setup->tpl->assign('dump', $setup->configDump());
} else {
	$setup->tpl->assign('version', $setup->getDbVersion());
}
$setup->tpl->assign('admins', $setup->checkAdmins());

$setup->tpl->display('step2.tpl');
?>