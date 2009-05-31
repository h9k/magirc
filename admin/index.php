<?php
// $Id$

ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');

$magirc_conf = '../conf/magirc.cfg.php';
$denora_conf = '../conf/denora.cfg.php';

if (!file_exists($magirc_conf)) {
	die('Please configure conf/magirc.cfg.dist.php and rename it to conf/magirc.cfg.php');
}
if (!file_exists($denora_conf)) {
	die('Please configure conf/denora.cfg.dist.php and rename it to conf/denora.cfg.php');
}
if (!is_writable('tmp/')) {
	die("The 'admin/tmp/' directory is not writable. Please chmod it to 0777.");
}

require_once('lib/init.inc.php');
require_once('lib/Admin.class.php');

$admin = new Admin();

if (!$admin->cfg->getParam('db_version')) {
	die('<html><head></head><body><div style="border: 3px solid #FF3300; background-color:#FFCC99; text-align:center; padding: 2px; margin: 10px;"><strong>ERROR:</strong> SQL Config Table is missing!<br />Please run the <em>Magirc Installer</em>.</div></body></html>');
}

if ($admin->cfg->getParam('debug_mode') < 1) {
	ini_set('display_errors','off');
	error_reporting(E_ERROR);
}

$magirc_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$magirc_url = explode("admin/",$magirc_url);
$magirc_url = $magirc_url[0];

session_start();
if (isset($_SESSION['loginUsername'])) {
	$page = (isset($_GET['page'])) ? $_GET['page'] : 'home';
} else {
	$page = (isset($_GET['page'])) ? $_GET['page'] : 'login';
}

if ($page == 'login' && isset($_POST['login'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	if ($admin->denora->login($username, $password)) {
		$_SESSION['loginUsername'] = $username;
		$_SESSION['loginIP'] = $_SERVER['REMOTE_ADDR'];
		$page = 'home';
	} else {
		$_SESSION['message'] = "Could not connect to the admin panel as '{$username}'";
		$page = 'logout';
	}
}

if ($page == 'logout') {
	$message = "";
	if (isset($_SESSION["loginUsername"])) {
		$message .= "{$_SESSION["loginUsername"]}, thanks for using phpDenora.";
		unset($_SESSION["loginUsername"]);
	}
	if (isset($_SESSION["message"])) {
		$message .= $_SESSION["message"];
		unset($_SESSION["message"]);
	}
	session_destroy();
	$admin->tpl->assign('message', $message);
	$admin->tpl->display('login.tpl');
} elseif ($page == "login") {
	$admin->tpl->display('login.tpl');
} elseif ($admin->sessionStatus()) {
	if (file_exists("inc/$page.php")) {
		include("inc/$page.php");
	} elseif ($admin->tpl->template_exists("$page.tpl")) {
		$admin->tpl->display("$page.tpl");
	} else {
		die("ERROR: unable to load page $page");
	}
} else {
	$admin->tpl->assign('message', "Access denied");
	$admin->tpl->display('login.tpl');
}
?>