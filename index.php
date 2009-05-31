<?php
// $Id$

/* -------------------------------------- *
 *     Magirc - Let the Magirc begin!     *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 *  http://magirc.org/                    *
 *  (c) 2009 hal9000@denorastats.org      *
 *  GPL v2 License - see doc/COPYING      *
 * -------------------------------------- *
 */

ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');

// load libs
include('lib/magirc/init.inc.php');

$magirc = new Magirc;

if ($ircd = $magirc->cfg->getParam('ircd_type')) {
	$magirc->denora->loadProtocol($ircd);
} else {
	$magirc->displayError("Unable to load config");
	exit;
}

define('DEBUG', $magirc->cfg->getParam('debug_mode'));
define('BASE_URL', $magirc->cfg->getParam('base_url'));
$magirc->tpl->template_dir = 'theme/'.$magirc->cfg->getParam('theme').'/tpl';
$magirc->tpl->config_dir = 'theme/'.$magirc->cfg->getParam('theme').'/cfg';

$section = $magirc->getUrlParameter('section');
$inc_file = 'inc/' . $section . '.inc.php';
if (file_exists($inc_file)) {
	require_once($inc_file);
} else {
	if ($content = $magirc->getPage($section)) {
		$magirc->tpl->assign('content', $content);
		$magirc->tpl->display('generic.tpl');
	} else {
		$magirc->displayError("The requested page '$section' does not exist");
		exit;
	}
}

?>
