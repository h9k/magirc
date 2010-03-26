<?php
// $Id$

ini_set('display_errors','on');
error_reporting(E_ALL); #E_NOTICE
ini_set('default_charset','UTF-8');

session_start();

$magirc_conf = '../conf/magirc.cfg.php';
$denora_conf = '../conf/denora.cfg.php';

if (!file_exists($magirc_conf)) {
    die('Please configure conf/magirc.cfg.dist.php and rename it to conf/magirc.cfg.php');
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
} else {
    $admin->tpl->force_compile = true;
    //$admin->tpl->debugging = true;
}

if (isset($_SESSION['username'])) {
    $page = (isset($_GET['page'])) ? $_GET['page'] : 'home';
} else {
    $page = (isset($_GET['page'])) ? $_GET['page'] : 'login';
}

if ($page == 'login' && isset($_POST['form'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($admin->login($username, $password)) {
        $_SESSION['username'] = $username;
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        $page = 'home';
    } else {
        $_SESSION['message'] = "Could not connect to the admin panel as '{$username}'";
        $page = 'logout';
    }
    $admin->tpl->assign('session', $_SESSION);
}

if ($page == 'logout') {
    $message = "";
    if (isset($_SESSION["username"])) {
        $message .= "{$_SESSION["username"]}, thanks for using MagIRC.";
        unset($_SESSION["username"]);
    }
    if (isset($_SESSION["message"])) {
        $message .= $_SESSION["message"];
        unset($_SESSION["message"]);
    }
    session_destroy();
    $admin->tpl->assign('session', array());
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