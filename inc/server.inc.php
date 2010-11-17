<?php
// $Id$

// Little dirty hack
if (!isset($_GET['action']) && isset($_GET['server'])) {
	$_GET['action'] = 'details';
}

require_once('lib/magirc/denora/Server.class.php');

switch(@$_GET['action']) {
	case null:
		$this->tpl->display('server.tpl');
		break;
	case 'details':
		if (isset($_GET['server'])) {
			$server = new Server($_GET['server']);
			$this->tpl->assign('server', $server);
			$this->tpl->display('server_details.tpl');
		}
		break;
	case 'list':
		$server = new Server(null);
		$data = $server->jsonList();
		include('lib/json/json_proxy.php');
		break;
	case 'json':
		#header('Content-type: application/json');
		$server = new Server(null);
		echo $server->jsonStats();
		break;
	default:	
}

?>