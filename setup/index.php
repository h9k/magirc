<?php
// $Id$

ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');

$_GET['step'] = (isset($_GET['step'])) ? htmlspecialchars($_GET['step']) : 1;
$magirc_conf = '../conf/magirc.cfg.php';
$denora_conf = '../conf/denora.cfg.php';
$config = array(); $sql = array();

if (!file_exists($magirc_conf)) {
	die('Please configure conf/magirc.cfg.dist.php and rename it to conf/magirc.cfg.php');
}
if (!file_exists($denora_conf)) {
	die('Please configure conf/denora.cfg.dist.php and rename it to conf/denora.cfg.php');
}
if (!is_writable('tmp/')) {
	die("The 'setup/tmp/' directory is not writable. Please chmod it to 0777.");
}

require_once('lib/init.inc.php');
require_once('lib/Setup.class.php');

$setup = new Setup();

// workaround fot $smarty var not working properly for some reason...
$setup->tpl->assign('get', @$_GET);
$setup->tpl->assign('post', @$_POST);

switch($_GET['step']) {
	case 1: // System requirements checks, SQL config check
		include('inc/step1.php');
		break;
	case 2: // Check denora version, display login form
		include('inc/step2.php');
		break;
	case 3: // Log in, Perform DB checks, create/update magirc config table, display link to admin panel
		include('inc/step3.php');
		break;
	default:
		echo "<pre><span style=\"color:red;\">Error:</span> unknown step $_GET[step]</pre>";
		break;
}

?>
