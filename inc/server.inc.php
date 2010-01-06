<?php
// $Id$

require_once('lib/magirc/denora/Server.class.php');

if (isset($_GET['server'])) {
	$server = new Server($_GET['server']);
	$this->tpl->assign('server', $server);
	$this->tpl->display('server_details.tpl');
} else {
	$this->tpl->assign('serverlist', $this->denora->getServers());
	$this->tpl->display('server.tpl');
}

?>