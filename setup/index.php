<?php
// $Id$

ini_set('display_errors','on');
error_reporting(E_ALL); # E_NOTICE
ini_set('default_charset','UTF-8');

$_GET['step'] = (isset($_GET['step'])) ? htmlspecialchars($_GET['step']) : 1;
$magirc_conf = '../conf/magirc.cfg.php';
$config = array();
$sql = array();

if (!is_writable('tmp/')) {
    die("ERROR: The 'setup/tmp/' directory is not writable. Please chmod it to 0777.");
}

require_once('lib/init.inc.php');
require_once('lib/Setup.class.php');

$setup = new Setup();

switch($_GET['step']) {
    case 1: // System requirements checks, SQL config check
        include('inc/step1.php');
        break;
    case 2: // Create/Update MagIRC SQL database, create admin user if necessary
        include('inc/step2.php');
        break;
    case 3: // Create admin user if necessary, display link to admin panel when done
        include('inc/step3.php');
        break;
    default:
        echo "<pre><span style=\"color:red;\">Error:</span> unknown step $_GET[step]</pre>";
        break;
}

?>
