<?php

$check = $setup->configCheck();
$setup->tpl->assign('check', $check);
if (!$check) { // Dump file to db
	$setup->tpl->assign('dump', $setup->configDump());
} else {
	$version = $setup->getDbVersion();
	$updated = false;
	if ($version != DB_VERSION) {
		if ($version < 2) {
			$setup->db->insert('magirc_config', array('parameter' => 'live_interval', 'value' => 15));
			$setup->db->insert('magirc_config', array('parameter' => 'cdn_enable', 'value' => 1));
		}
		if ($version < 3) {
			$setup->db->insert('magirc_config', array('parameter' => 'rewrite_enable', 'value' => 0));
		}
		$setup->db->update('magirc_config', array('value' => DB_VERSION), array('parameter' => 'db_version'));
		$updated = true;
	}
	$setup->tpl->assign('version', DB_VERSION);
	$setup->tpl->assign('updated', $updated);
}
$setup->tpl->assign('admins', $setup->checkAdmins());

$setup->tpl->display('step2.tpl');
?>