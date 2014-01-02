<?php
/**
 * MagIRC - Let the magirc begin!
 * Frontend
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2012 - 2014 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * @version     0.9.0
 */

ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');
date_default_timezone_set('UTC');

if (version_compare(PHP_VERSION, '5.3.0', '<') || !extension_loaded('pdo') || !in_array('mysql', PDO::getAvailableDrivers()) || !extension_loaded('gettext') || !extension_loaded('mcrypt') || get_magic_quotes_gpc()) die('ERROR: System requirements not met. Please run Setup.');
if (!file_exists('conf/magirc.cfg.php')) die('ERROR: MagIRC is not configured. Please run Setup.');
if (!is_writable('tmp/')) die('ERROR: Unable to write temporary files. Please run Setup.');

// load libs
include_once('lib/magirc/version.inc.php');
if (file_exists('vendor/autoload.php')) {
	require 'vendor/autoload.php';
} else {
	die('Please run the `composer install` or `php composer.phar install` command. See README for more information');
}
require_once('lib/magirc/DB.class.php');
require_once('lib/magirc/Config.class.php');
require_once('lib/magirc/Magirc.class.php');
require_once('lib/magirc/services/Service.interface.php');
require_once('lib/magirc/services/Anope.class.php');
require_once('lib/magirc/services/Denora.class.php');
require_once('lib/magirc/objects/ServerBase.class.php');
require_once('lib/magirc/objects/ChannelBase.class.php');
require_once('lib/magirc/objects/UserBase.class.php');

$magirc = new Magirc;

try {
	define('DEBUG', $magirc->cfg->debug_mode);
	date_default_timezone_set($magirc->cfg->timezone);
	define('BASE_URL', $magirc->cfg->base_url);
	$magirc->tpl->template_dir = 'theme/'.$magirc->cfg->theme.'/tpl';
	$magirc->tpl->config_dir = 'theme/'.$magirc->cfg->theme.'/cfg';
	$magirc->tpl->assign('cfg', $magirc->cfg);
	$locales = array();
	foreach (glob("locale/*") as $filename) {
		if (is_dir($filename)) $locales[] = basename($filename);
	}
	$magirc->tpl->assign('locales', $locales);
	if ($magirc->cfg->db_version < DB_VERSION) die('Upgrade in progress. Please wait a few minutes, thank you.');

	if ($magirc->cfg->debug_mode < 1) {
		ini_set('display_errors','off');
		error_reporting(E_ERROR);
	} else {
		$magirc->tpl->force_compile = true;
		/*if ($magirc->cfg->debug_mode') > 1) {
			$magirc->tpl->debugging = true;
		}*/
	}

	$magirc->slim->notFound(function() use ($magirc) {
		$magirc->tpl->assign('err_msg', 'HTTP 404 - Not Found');
		$magirc->tpl->assign('err_extra', null);
		$magirc->tpl->display('error.tpl');
	});
	
	include_once('theme/' . $magirc->cfg->theme . '/slim/routes.inc.php');	

	$magirc->slim->run();
} catch (Exception $e) {
	$magirc->tpl->assign('err_msg', $e->getMessage());
	$magirc->tpl->assign('err_extra', $e->getTraceAsString());
	$magirc->tpl->display('error.tpl');
}
?>
