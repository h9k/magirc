<?php
// $Id$

/* -------------------------------------- *
 *     MagIRC - Let the magirc begin!     *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 *  http://www.magirc.org/                *
 *  (c) 2009-2012 hal9000@denorastats.org *
 *  GPL v2 License - see doc/COPYING      *
 * -------------------------------------- *
 */

ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');
if (get_magic_quotes_gpc()) die('Disable magic_quotes_gpc in your php.ini');

// load libs
include_once('lib/magirc/version.inc.php');
require_once('lib/smarty/Smarty.class.php');
require_once('lib/magirc/DB.class.php');
require_once('lib/magirc/Config.class.php');
require_once('lib/magirc/Magirc.class.php');
#require_once('lib/magirc/anope/Anope.class.php');
require_once('lib/magirc/denora/Denora.class.php');

$magirc = new Magirc;

try {
	define('DEBUG', $magirc->cfg->getParam('debug_mode'));
	$protocol = @$_SERVER['HTTPS'] ? 'https://' : 'http://';
	define('BASE_URL', $protocol.$_SERVER['SERVER_NAME'].str_replace('index.php', '', $_SERVER['PHP_SELF']));
	$magirc->tpl->template_dir = 'theme/'.$magirc->cfg->getParam('theme').'/tpl';
	$magirc->tpl->config_dir = 'theme/'.$magirc->cfg->getParam('theme').'/cfg';

	if ($magirc->cfg->getParam('debug_mode') < 1) {
		ini_set('display_errors','off');
		error_reporting(E_ERROR);
	} else {
		$magirc->tpl->force_compile = true;
		if ($magirc->cfg->getParam('debug_mode') > 1) {
			#$magirc->tpl->debugging = true;
		}
	}
	// Little dirty hack
	if (!isset($_GET['section'])) {
		$_GET['section'] = 'home';
	}
	$magirc->display();
} catch (Exception $e) {
	$magirc->displayError($e->getMessage());
}
?>
