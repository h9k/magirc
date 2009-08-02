<?php
// $Id$

require_once('lib/magirc/denora/Status.class.php');

$status = new Status();
$magirc->tpl->assign('status', $status);
$magirc->tpl->display('status.tpl');

?>