<?php
/**
 * MagIRC - Let the magirc begin!
 * RESTful API
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2012 - 2015 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * @version     1.0.2
 **/

ini_set('display_errors','off');
error_reporting(E_ALL);
ini_set('default_charset','UTF-8');
date_default_timezone_set('UTC');

include_once('../lib/magirc/version.inc.php');
require_once('../lib/magirc/DB.class.php');
require_once('../lib/magirc/Config.class.php');
require_once('../lib/magirc/services/Service.interface.php');
require_once('../lib/magirc/services/Anope.class.php');
require_once('../lib/magirc/services/Denora.class.php');
require_once('../lib/magirc/objects/ServerBase.class.php');
require_once('../lib/magirc/objects/ChannelBase.class.php');
require_once('../lib/magirc/objects/UserBase.class.php');
require_once('../lib/magirc/Magirc.class.php');
require '../vendor/autoload.php';

// Initialization
$magirc = new Magirc('service');
//NOTE: we need to use HTTP 1.0 because nginx might chunk otherwise
$magirc->slim->config('http.version', '1.0');
$magirc->slim->contentType('application/json');
$magirc->slim->notFound(function() use($magirc) {
    $magirc->jsonOutput(array('error' => "HTTP 404 Not Found"));
});
date_default_timezone_set($magirc->cfg->timezone);

// Routing definitions

$magirc->slim->get('/network/status', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getCurrentStatus());
});

$magirc->slim->get('/network/max', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getMaxValues());
});

$magirc->slim->get('/network/clients/percent', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->makeClientPieData($magirc->service->getClientStats(), $magirc->service->getUserCount()));
});

$magirc->slim->get('/network/clients', function($chan = null) use($magirc) {
	$magirc->jsonOutput($magirc->service->getClientStats(), true);
});

$magirc->slim->get('/network/countries/percent', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->makeCountryPieData($magirc->service->getCountryStats(), $magirc->service->getUserCount()));
});

$magirc->slim->get('/network/countries', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getCountryStats(), true);
});

$magirc->slim->get('/network/countries/map', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getCountryMap());
});

$magirc->slim->get('/servers', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getServerList(), true, 'server');
});

$magirc->slim->get('/servers/history', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getServerHistory());
});

$magirc->slim->get('/servers/:server', function($server) use($magirc) {
	$magirc->jsonOutput($magirc->service->getServer($server));
});

$magirc->slim->get('/servers/:server/clients/percent', function($server) use($magirc) {
	$magirc->jsonOutput($magirc->service->makeClientPieData($magirc->service->getClientStats('server', $server), $magirc->service->getUserCount('server', $server)));
});

$magirc->slim->get('/servers/:server/clients', function($server) use($magirc) {
	$magirc->jsonOutput($magirc->service->getClientStats('server', $server), true);
});

$magirc->slim->get('/servers/:server/countries/percent', function($server) use($magirc) {
	$magirc->jsonOutput($magirc->service->makeCountryPieData($magirc->service->getCountryStats('server', $server), $magirc->service->getUserCount('server', $server)));
});

$magirc->slim->get('/servers/:server/countries', function($server) use($magirc) {
	$magirc->jsonOutput($magirc->service->getCountryStats('server', $server), true);
});

$magirc->slim->get('/channels', function() use($magirc) {
    $magirc->jsonOutput($magirc->service->getChannelList(@$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/channels/history', function() use($magirc) {
    $magirc->jsonOutput($magirc->service->getChannelHistory());
});

$magirc->slim->get('/channels/biggest(/:limit)', function($limit = 10) use($magirc) {
	$magirc->jsonOutput($magirc->service->getChannelBiggest((int) $limit), true, 'channel');
});

$magirc->slim->get('/channels/top(/:limit)', function($limit = 10) use($magirc) {
	$magirc->jsonOutput($magirc->service->getChannelTop((int) $limit), true, 'channel');
});

$magirc->slim->get('/channels/activity/:type', function($type) use($magirc) {
	$magirc->jsonOutput($magirc->service->getChannelGlobalActivity($type, @$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/channels/:chan', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getChannel($chan));
});

$magirc->slim->get('/channels/:chan/users', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getChannelUsers($chan), true, 'nickname');
});

$magirc->slim->get('/channels/:chan/activity/:type', function($chan, $type) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getChannelActivity($chan, $type, @$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/channels/:chan/hourly/:type', function($chan, $type) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getChannelHourlyActivity($chan, $type));
});

$magirc->slim->get('/channels/:chan/checkstats', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->checkChannelStats($chan));
});

$magirc->slim->get('/channels/:chan/clients/percent', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->makeClientPieData($magirc->service->getClientStats('channel', $chan), $magirc->service->getUserCount('channel', $chan)));
});

$magirc->slim->get('/channels/:chan/clients', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getClientStats('channel', $chan), true);
});

$magirc->slim->get('/channels/:chan/countries/percent', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->makeCountryPieData($magirc->service->getCountryStats('channel', $chan), $magirc->service->getUserCount('channel', $chan)));
});

$magirc->slim->get('/channels/:chan/countries', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getCountryStats('channel', $chan), true);
});

$magirc->slim->get('/users/history', function() use($magirc) {
    $magirc->jsonOutput($magirc->service->getUserHistory());
});

$magirc->slim->get('/users/top(/:limit)', function($limit = 10) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUsersTop((int) $limit), true, 'uname');
});

$magirc->slim->get('/users/activity/:type', function($type) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserGlobalActivity($type, @$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/users/:mode/:user', function($mode, $user) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUser($mode, $user));
});

$magirc->slim->get('/users/:mode/:user/channels', function($mode, $user) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserChannels($mode, $user));
});

$magirc->slim->get('/users/:mode/:user/activity(/:chan)', function($mode, $user, $chan = null) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserActivity($mode, $user, $chan), true);
});

$magirc->slim->get('/users/:mode/:user/hourly/(:chan/):type', function($mode, $user, $chan = null, $type) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserHourlyActivity($mode, $user, $chan, $type));
});

$magirc->slim->get('/users/:mode/:user/checkstats', function($mode, $user) use($magirc) {
	$magirc->jsonOutput($magirc->service->checkUserStats($user, $mode));
});

$magirc->slim->get('/operators', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getOperatorList(), true, 'nickname');
});

// Go! :)
$magirc->slim->run();
