<?php
// $Id$

require_once('lib/magirc/denora/Channel.class.php');
$c = new Channel();

if (isset($_GET['channel'])) {
	$magirc->tpl->assign('channel', $c->getChannel(urldecode($_GET['channel'])));
	$magirc->tpl->display('channel_details.tpl');
} else {
	$magirc->tpl->assign('chanlist', $c->getChannels());
	$magirc->tpl->display('channel.tpl');
}

?>