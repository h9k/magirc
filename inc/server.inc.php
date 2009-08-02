<?php
// $Id$

require_once('lib/magirc/denora/Server.class.php');

if (isset($_GET['server'])) {
	$server = new Server($_GET['server']);
	$magirc->tpl->assign('server', $server);
	$magirc->tpl->display('server_details.tpl');
} else {
	$magirc->tpl->assign('serverlist', $magirc->denora->getServers());
	$magirc->tpl->display('server.tpl');
}

?>