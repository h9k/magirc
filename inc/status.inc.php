<?php
// $Id$

require_once('lib/magirc/denora/Status.class.php');

$status = new Status();
$this->tpl->assign('status', $status);
$this->tpl->display('status.tpl');

?>