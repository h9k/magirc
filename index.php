<?php
/**
 * MagIRC - Let the magirc begin!
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2012 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see doc/LICENSE
 * @version     0.7.0
 */

ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');
if (get_magic_quotes_gpc()) die('Disable magic_quotes_gpc in your php.ini');

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
	define('DEBUG', $magirc->cfg->getParam('debug_mode'));
	define('BASE_URL', sprintf("%s://%s%s", @$_SERVER['HTTPS'] ? 'https' : 'http', $_SERVER['SERVER_NAME'], $_SERVER['SCRIPT_NAME']));
	$magirc->tpl->template_dir = 'theme/'.$magirc->cfg->getParam('theme').'/tpl';
	$magirc->tpl->config_dir = 'theme/'.$magirc->cfg->getParam('theme').'/cfg';

	if ($magirc->cfg->getParam('debug_mode') < 1) {
		ini_set('display_errors','off');
		error_reporting(E_ERROR);
	} else {
		$magirc->tpl->force_compile = true;
		/*if ($magirc->cfg->getParam('debug_mode') > 1) {
			$magirc->tpl->debugging = true;
		}*/
	}
	
	$magirc->slim->notFound(function () use ($magirc) {
		$magirc->tpl->assign('err_msg', 'HTTP 404 - Not Found');
		$magirc->tpl->display('error.tpl');
	});
	
	$magirc->slim->get('/(home)', function() use($magirc) {
		$magirc->tpl->assign('welcome', $magirc->cfg->getParam('msg_welcome'));
		$magirc->tpl->display('home.tpl');
	});
	$magirc->slim->get('/:section/:target/:action', function($section, $target, $action) use($magirc) {
		$tpl_file = basename($section) . '_' . basename($action) . '.tpl';
		$tpl_path = 'theme/' . $magirc->cfg->getParam("theme") . '/tpl/' . $tpl_file;
		if (file_exists($tpl_path)) {
			$magirc->tpl->assign('section', $section);
			$magirc->tpl->assign('target', $target);
			$magirc->tpl->display($tpl_file);
		} else {
			$magirc->slim->notFound();
		}
	});
	$magirc->slim->get('/:section(/:action)', function($section, $action = 'main') use($magirc) {
		$tpl_file = basename($section) . '_' . basename($action) . '.tpl';
		$tpl_path = 'theme/' . $magirc->cfg->getParam("theme") . '/tpl/' . $tpl_file;
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
	$magirc->tpl->display('error.tpl');
}
?>
