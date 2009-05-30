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

// read config and load libs
include('conf/magirc.cfg.php') or die ('magirc.cfg.php configuration file missing');
include('conf/db.cfg.php') or die ('db.cfg.php configuration file missing');
include('lib/magirc/init.inc.php');

$magirc =& new Magirc;

$inc_file = 'inc/' . $magirc->getUrlParameter('section') . '.inc.php';
if (file_exists($inc_file)) {
	require_once($inc_file);
} else {
	if ($content = $magirc->getPage($page)) {
		$magirc->tpl->assign('content', $content);
		$magirc->tpl->display('generic.tpl');
	} else {
		$magirc->tpl->assign('err_msg', 'The requested page does not exist');
		$magirc->tpl->display('error.tpl');
	}
}

?>
