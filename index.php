<?php
/**
 * MagIRC - Let the magirc begin!
 * Frontend
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2012 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * @version     0.8.6
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
require_once('lib/slim/Slim.php');
require_once('lib/smarty/Smarty.class.php');
require_once('lib/magirc/DB.class.php');
require_once('lib/magirc/Config.class.php');
require_once('lib/magirc/Magirc.class.php');
require_once('lib/magirc/denora/Denora.class.php');

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

	$magirc->slim->get('/(network)', function() use($magirc) {
		$magirc->tpl->assign('section', 'network');
		$magirc->tpl->display('network_main.tpl');
	});
	$magirc->slim->get('/content/:name', function($name) use($magirc) {
		echo $magirc->getContent($name);
	});
	$magirc->slim->get('/:section/:target/:action', function($section, $target, $action) use($magirc) {
		$tpl_file = basename($section) . '_' . basename($action) . '.tpl';
		$tpl_path = 'theme/' . $magirc->cfg->theme . '/tpl/' . $tpl_file;
		if (file_exists($tpl_path)) {
			$mode = null;
			if ($section == 'channel') {
				switch ($magirc->denora->checkChannel($target)) {
					case 404: $magirc->slim->notFound();
					case 403: $magirc->slim->halt(403, 'Access denied');
				}
			} elseif ($section == 'user') {
				$array = explode(':', $target);
				if (count($array) == 2) {
					$mode = $array[0];
					$target = $array[1];
					if (!$magirc->denora->checkUser($target, $mode)) {
						$magirc->slim->notFound();
					}
				} else {
					$magirc->slim->notFound();
				}
			}
			$magirc->tpl->assign('section', $section);
			$magirc->tpl->assign('target', $target);
			$magirc->tpl->assign('mode', $mode);
			$magirc->tpl->display($tpl_file);
		} else {
			$magirc->slim->notFound();
		}
	});
	$magirc->slim->get('/:section(/:action)', function($section, $action = 'main') use($magirc) {
		$tpl_file = basename($section) . '_' . basename($action) . '.tpl';
		$tpl_path = 'theme/' . $magirc->cfg->theme . '/tpl/' . $tpl_file;
		if (file_exists($tpl_path)) {
			$magirc->tpl->assign('section', $section);
			$magirc->tpl->display($tpl_file);
		} else {
			$magirc->slim->notFound();
		}
	});

	$magirc->slim->run();
} catch (Exception $e) {
	$magirc->tpl->assign('err_msg', $e->getMessage());
	$magirc->tpl->assign('err_extra', $e->getTraceAsString());
	$magirc->tpl->display('error.tpl');
}
?>
