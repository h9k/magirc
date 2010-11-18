<?php
// $Id$

if (isset($_POST['form'])) {
	if (isset($_POST['ircd_type'])) $admin->saveConfig('ircd_type',$_POST['ircd_type']);
	if (isset($_POST['hide_ulined'])) $admin->saveConfig('hide_ulined',1);
	else $admin->saveConfig('hide_ulined',0);
	if (isset($_POST['hide_servers'])) $admin->saveConfig('hide_servers',$_POST['hide_servers']);
	else $admin->saveConfig('hide_servers','');
	if (isset($_POST['hide_chans'])) $admin->saveConfig('hide_chans',$_POST['hide_chans']);
	else $admin->saveConfig('hide_chans','');
	$admin->tpl->assign('success', true);
}

$ircds = array();
foreach (glob("../lib/magirc/denora/protocol/*") as $filename) {
	if ($filename != "../lib/magirc/denora/protocol/index.php") {
		$ircdlist = explode("/", $filename);
		$ircdlist = explode(".", $ircdlist[5]);
		$ircds[] = $ircdlist[0];
	}
}

$admin->tpl->assign('ircds', $ircds);
$admin->tpl->display('network.tpl');

?>