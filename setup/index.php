<?php
/**
 * MagIRC - Let the magirc begin!
 * Installer
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2012 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * @version     0.8.1
 */

ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');
date_default_timezone_set('UTC');

$_GET['step'] = (isset($_GET['step'])) ? htmlspecialchars($_GET['step']) : 1;
define('MAGIRC_CFG_FILE', '../conf/magirc.cfg.php');

if (!is_writable('../tmp/')) die("ERROR: The 'tmp/' directory is not writable. Please chmod it to 0777.");

include_once('../lib/magirc/version.inc.php');
require_once('../lib/smarty/Smarty.class.php');
require_once('../lib/magirc/DB.class.php');
require_once('lib/Setup.class.php');

$setup = new Setup();

switch($_GET['step']) {
	case 1: // System requirements checks
		include('inc/step1.php');
		break;
	case 2: // SQL config check, Create/Update MagIRC SQL database
		include('inc/step2.php');
		break;
	case 3: // Create admin user if necessary, display link to admin panel when done
		include('inc/step3.php');
		break;
	case 4: // Installation successful
		include('inc/step4.php');
		break;
	default:
		die("ERROR: Unknown step {$_GET['step']}");
}

?>
