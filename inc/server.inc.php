<?php
// $Id$

// Little dirty hack
if (!isset($_GET['action'])) {
	if (!isset($_GET['server'])) {
		$_GET['action'] = 'list';
	} else {
		$_GET['action'] = 'details';
	}
}

require_once('lib/magirc/denora/Server.class.php');

switch($_GET['action']) {
	case null:
	case 'list':
		$this->tpl->assign('serverlist', $this->denora->getServers());
		$this->tpl->display('server.tpl');
		break;
	case 'details':
		if (isset($_GET['server'])) {
			$server = new Server($_GET['server']);
			$this->tpl->assign('server', $server);
			$this->tpl->display('server_details.tpl');
		}
		break;
	case 'json':
		header('Content-type: application/json');
		echo Server::jsonList();
		break;
	default:	
}

?>