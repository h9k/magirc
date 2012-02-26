<?php
// $Id$

// Little dirty hack
if (!isset($_GET['action']) && isset($_GET['server'])) {
	$_GET['action'] = 'details';
}

switch(@$_GET['action']) {
	case null:
		$this->tpl->display('server.tpl');
		break;
}

?>