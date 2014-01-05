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
require_once('lib/magirc/objects/ServerBase.class.php');
require_once('lib/magirc/objects/ChannelBase.class.php');
require_once('lib/magirc/objects/UserBase.class.php');

$magirc = new Magirc(true);

include_once('theme/' . $magirc->cfg->theme . '/slim/routes.inc.php');

$magirc->slim->run();
