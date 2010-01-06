<?php
// $Id$

// Little dirty hack
if (!isset($_GET['action'])) {
	if (!isset($_GET['channel'])) {
		$_GET['action'] = 'list';
	} else {
		$_GET['action'] = 'details';
	}
}

require_once('lib/magirc/denora/Channel.class.php');
$c = new Channel();

$this->tpl->assign('mirc', $this->cfg->getParam('mirc_url'));
$this->tpl->assign('webchat', $this->cfg->getParam('webchat_url'));

switch($_GET['action']) {
	case 'details':
		$this->tpl->assign('channel', $c->getChannel(urldecode($_GET['channel'])));
		$this->tpl->display('channel_details.tpl');
		break;
	case 'list':
		$this->tpl->assign('chanlist', $c->getChannels());
		$this->tpl->display('channel.tpl');
		break;
	default:
		$this->displayError("Unknown action");
}



?>