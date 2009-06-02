<?php
// $Id: status.inc.php 34 2009-06-01 11:10:30Z hal9000 $

if (isset($_GET['server'])) {
	$server = new Server($_GET['server']);
	$magirc->tpl->assign('server', $server);
	$magirc->tpl->display('server_details.tpl');
} else {
	$magirc->tpl->assign('serverlist', $magirc->denora->getServers());
	$magirc->tpl->display('server.tpl');
}

?>