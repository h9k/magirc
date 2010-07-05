<?php
// $Id$

/* -------------------------------------- *
 *     MagIRC - Let the Magirc begin!     *
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ *
 *  http://www.magirc.org/                *
 *  (c) 2009-2010 hal9000@denorastats.org *
 *  GPL v2 License - see doc/COPYING      *
 * -------------------------------------- *
*/

ini_set('display_errors','on');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');

// load libs
include('lib/magirc/init.inc.php');

$magirc = new Magirc;

if (date('m') == 4 && date('d') == 1) {
	$magirc->displayError("Eggs not found");
	exit;
}

try {
    define('DEBUG', $magirc->cfg->getParam('debug_mode'));
    define('BASE_URL', $magirc->cfg->getParam('base_url'));
    $magirc->tpl->template_dir = 'theme/'.$magirc->cfg->getParam('theme').'/tpl';
    $magirc->tpl->config_dir = 'theme/'.$magirc->cfg->getParam('theme').'/cfg';

    if ($magirc->cfg->getParam('debug_mode') < 1) {
        ini_set('display_errors','off');
        error_reporting(E_ERROR);
    } else {
        $magirc->tpl->force_compile = true;
        /*if ($magirc->cfg->getParam('debug_mode') > 1)
            $magirc->tpl->debugging = true;*/
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
