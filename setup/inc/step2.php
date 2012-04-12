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
		if ($version < 4) {
			$setup->db->insert('magirc_config', array('parameter' => 'timezone', 'value' => 'UTC'));
		}
		if ($version < 5) {
			$setup->db->insert('magirc_config', array('parameter' => 'welcome_mode', 'value' => 'statuspage'));
			$setup->db->query("CREATE TABLE IF NOT EXISTS `magirc_content` (
				`name` varchar(16) NOT NULL default '', `text` text NOT NULL default '',
				PRIMARY KEY (`name`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			$welcome_msg = $setup->db->selectOne('magirc_config', array('parameter' => 'msg_welcome'));
			$setup->db->insert('magirc_content', array('name' => 'welcome', 'text' => $welcome_msg['value']));
			$setup->db->delete('magirc_config', array('parameter' => 'msg_welcome'));
			$setup->db->query("ALTER TABLE `magirc_config` CHANGE `value` `value` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''");
			$setup->db->query("ALTER TABLE `magirc_config` ENGINE = InnoDB");
		}
		if ($version < 6) {
			$setup->db->insert('magirc_config', array('parameter' => 'block_spchans', 'value' => 0));
			$setup->db->insert('magirc_config', array('parameter' => 'net_roundrobin', 'value' => ''));
			$setup->db->insert('magirc_config', array('parameter' => 'service_adsense_id', 'value' => ''));
			$setup->db->insert('magirc_config', array('parameter' => 'service_adsense_channel', 'value' => ''));
			$setup->db->insert('magirc_config', array('parameter' => 'service_searchirc', 'value' => ''));
			$setup->db->insert('magirc_config', array('parameter' => 'service_netsplit', 'value' => ''));
		}
		if ($version < 7) {
			$setup->db->insert('magirc_config', array('parameter' => 'server_href', 'value' => 0));
			$setup->db->insert('magirc_config', array('parameter' => 'channel_href', 'value' => 0));
			$setup->db->insert('magirc_config', array('parameter' => 'net_sslroundrobin', 'value' => ''));
			$setup->db->insert('magirc_config', array('parameter' => 'net_defaulthref', 'value' => ''));
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