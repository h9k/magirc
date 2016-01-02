<?php
/**
 * MagIRC - Let the magirc begin!
 * RESTful API
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2012 - 2016 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * @version     1.5.0
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

$magirc->slim->get('/network/clients', function() use($magirc) {
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

$magirc->slim->get('/servers/{server}', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->getServer($args['server']));
});

$magirc->slim->get('/servers/{server}/clients/percent', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->makeClientPieData($magirc->service->getClientStats('server', $args['server']), $magirc->service->getUserCount('server', $args['server'])));
});

$magirc->slim->get('/servers/{server}/clients', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->getClientStats('server', $args['server']), true);
});

$magirc->slim->get('/servers/{server}/countries/percent', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->makeCountryPieData($magirc->service->getCountryStats('server', $args['server']), $magirc->service->getUserCount('server', $args['server'])));
});

$magirc->slim->get('/servers/{server}/countries', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->getCountryStats('server', $args['server']), true);
});

$magirc->slim->get('/channels', function() use($magirc) {
    $magirc->jsonOutput($magirc->service->getChannelList(@$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/channels/history', function() use($magirc) {
    $magirc->jsonOutput($magirc->service->getChannelHistory());
});

$magirc->slim->get('/channels/biggest[/{limit}]', function($req, $res, $args) use($magirc) {
	$limit = isset($args['limit']) ? $args['limit'] : 10;
	$magirc->jsonOutput($magirc->service->getChannelBiggest((int) $limit), true, 'channel');
});

$magirc->slim->get('/channels/top[/{limit}]', function($req, $res, $args) use($magirc) {
	$limit = isset($args['limit']) ? $args['limit'] : 10;
	$magirc->jsonOutput($magirc->service->getChannelTop((int) $limit), true, 'channel');
});

$magirc->slim->get('/channels/activity/{type}', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->getChannelGlobalActivity($args['type'], @$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/channels/{chan}', function($req, $res, $args) use($magirc) {
	$magirc->checkPermission('channel', $args['chan']);
	$magirc->jsonOutput($magirc->service->getChannel($args['chan']));
});

$magirc->slim->get('/channels/{chan}/users', function($req, $res, $args) use($magirc) {
	$magirc->checkPermission('channel', $args['chan']);
	$magirc->jsonOutput($magirc->service->getChannelUsers($args['chan']), true, 'nickname');
});

$magirc->slim->get('/channels/{chan}/activity/{type}', function($req, $res, $args) use($magirc) {
	$magirc->checkPermission('channel', $args['chan']);
	$magirc->jsonOutput($magirc->service->getChannelActivity($args['chan'], $args['type'], @$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/channels/{chan}/hourly/{type}', function($req, $res, $args) use($magirc) {
	$magirc->checkPermission('channel', $args['chan']);
	$magirc->jsonOutput($magirc->service->getChannelHourlyActivity($args['chan'], $args['type']));
});

$magirc->slim->get('/channels/{chan}/checkstats', function($req, $res, $args) use($magirc) {
	$magirc->checkPermission('channel', $args['chan']);
	$magirc->jsonOutput($magirc->service->checkChannelStats($args['chan']));
});

$magirc->slim->get('/channels/{chan}/clients/percent', function($req, $res, $args) use($magirc) {
	$magirc->checkPermission('channel', $args['chan']);
	$magirc->jsonOutput($magirc->service->makeClientPieData($magirc->service->getClientStats('channel', $args['chan']), $magirc->service->getUserCount('channel', $args['chan'])));
});

$magirc->slim->get('/channels/{chan}/clients', function($req, $res, $args) use($magirc) {
	$magirc->checkPermission('channel', $args['chan']);
	$magirc->jsonOutput($magirc->service->getClientStats('channel', $args['chan']), true);
});

$magirc->slim->get('/channels/{chan}/countries/percent', function($req, $res, $args) use($magirc) {
	$magirc->checkPermission('channel', $args['chan']);
	$magirc->jsonOutput($magirc->service->makeCountryPieData($magirc->service->getCountryStats('channel', $args['chan']), $magirc->service->getUserCount('channel', $args['chan'])));
});

$magirc->slim->get('/channels/{chan}/countries', function($req, $res, $args) use($magirc) {
	$magirc->checkPermission('channel', $args['chan']);
	$magirc->jsonOutput($magirc->service->getCountryStats('channel', $args['chan']), true);
});

$magirc->slim->get('/users/history', function() use($magirc) {
    $magirc->jsonOutput($magirc->service->getUserHistory());
});

$magirc->slim->get('/users/top[/{limit}]', function($req, $res, $args) use($magirc) {
	$limit = isset($args['limit']) ? $args['limit'] : 10;
	$magirc->jsonOutput($magirc->service->getUsersTop((int) $limit), true, 'uname');
});

$magirc->slim->get('/users/activity/{type}', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserGlobalActivity($args['type'], @$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/users/{mode}/{user}', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUser($args['mode'], $args['user']));
});

$magirc->slim->get('/users/{mode}/{user}/channels', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserChannels($args['mode'], $args['user']));
});

$magirc->slim->get('/users/{mode}/{user}/activity[/{chan}]', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserActivity($args['mode'], $args['user'], $args['chan']), true);
});

$magirc->slim->get('/users/{mode}/{user}/hourly/{type}', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserHourlyActivity($args['mode'], $args['user'], null, $args['type']));
});

$magirc->slim->get('/users/{mode}/{user}/hourly/{chan}/{type}', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserHourlyActivity($args['mode'], $args['user'], $args['chan'], $args['type']));
});

$magirc->slim->get('/users/{mode}/{user}/checkstats', function($req, $res, $args) use($magirc) {
	$magirc->jsonOutput($magirc->service->checkUserStats($args['user'], $args['mode']));
});

$magirc->slim->get('/operators', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getOperatorList(), true, 'nickname');
});

// Go! :)
$magirc->slim->run();
