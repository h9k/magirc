<?php

$check = $setup->configCheck();
$setup->tpl->assign('check', $check);
if (!$check) { // Dump file to db
	$setup->tpl->assign('dump', $setup->configDump());
} else {
	$current_dbversion = 2;
	$version = $setup->getDbVersion();
	$updated = true;
	switch ($version) {
		case 2:
			$updated = false;
			break;
		case 1:
			$setup->db->insert('magirc_config', array('parameter' => 'live_interval', 'value' => 15));
			$setup->db->insert('magirc_config', array('parameter' => 'cdn_enable', 'value' => 1));
	}
	if ($updated) {
		$setup->db->update('magirc_config', array('value' => $current_dbversion), array('parameter' => 'db_version'));
	}
	$setup->tpl->assign('version', $current_dbversion);
	$setup->tpl->assign('updated', $updated);
}
$setup->tpl->assign('admins', $setup->checkAdmins());

$setup->tpl->display('step2.tpl');
?>