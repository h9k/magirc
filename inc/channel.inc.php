<?php
// $Id$

require_once('lib/magirc/denora/Channel.class.php');
$c = new Channel();

if (isset($_GET['channel'])) {
	$this->tpl->assign('channel', $c->getChannel(urldecode($_GET['channel'])));
	$this->tpl->display('channel_details.tpl');
} else {
	$this->tpl->assign('chanlist', $c->getChannels());
	$this->tpl->display('channel.tpl');
}

?>