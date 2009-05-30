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

ini_set('display_errors','off');
error_reporting(E_ALL);

// read config and load libs
if (file_exists('conf/magirc.cfg.php')) {
	include('conf/magirc.cfg.php');
	if (DEBUG) {
		ini_set('display_errors','on');
	}
} else {
	die ('magirc.cfg.php configuration file missing');
}
if (file_exists('conf/db.cfg.php')) {
	include('conf/db.cfg.php');
} else {
	die ('db.cfg.php configuration file missing');
}
include('lib/magirc/init.inc.php');

$magirc =& new Magirc;

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
	}
}

?>
