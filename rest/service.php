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

// Routing definitions

$magirc->slim->get('/network/status', function($req, $res) use($magirc) {
    return $res->withJson($magirc->service->getCurrentStatus());
});

$magirc->slim->get('/network/max', function($req, $res) use($magirc) {
    return $res->withJson($magirc->service->getMaxValues());
});

$magirc->slim->get('/network/clients/percent', function($req, $res) use($magirc) {
    return $res->withJson($magirc->service->makeClientPieData($magirc->service->getClientStats(), $magirc->service->getUserCount()));
});

$magirc->slim->get('/network/clients', function($req, $res) use($magirc) {
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getClientStats()));
});

$magirc->slim->get('/network/countries/percent', function($req, $res) use($magirc) {
    return $res->withJson($magirc->service->makeCountryPieData($magirc->service->getCountryStats(), $magirc->service->getUserCount()));
});

$magirc->slim->get('/network/countries', function($req, $res) use($magirc) {
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getCountryStats()));
});

$magirc->slim->get('/network/countries/map', function($req, $res) use($magirc) {
    return $res->withJson($magirc->service->getCountryMap());
});

$magirc->slim->get('/servers', function($req, $res) use($magirc) {
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getServerList(), 'server'));
});

$magirc->slim->get('/servers/history', function($req, $res) use($magirc) {
    return $res->withJson($magirc->service->getServerHistory());
});

$magirc->slim->get('/servers/{server}', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->service->getServer($args['server']));
});

$magirc->slim->get('/servers/{server}/clients/percent', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->service->makeClientPieData($magirc->service->getClientStats('server', $args['server']), $magirc->service->getUserCount('server', $args['server'])));
});

$magirc->slim->get('/servers/{server}/clients', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getClientStats('server', $args['server'])));
});

$magirc->slim->get('/servers/{server}/countries/percent', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->service->makeCountryPieData($magirc->service->getCountryStats('server', $args['server']), $magirc->service->getUserCount('server', $args['server'])));
});

$magirc->slim->get('/servers/{server}/countries', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getCountryStats('server', $args['server'])));
});

$magirc->slim->get('/channels', function($req, $res) use($magirc) {
    return $res->withJson($magirc->service->getChannelList(@$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/channels/history', function($req, $res) use($magirc) {
    return $res->withJson($magirc->service->getChannelHistory());
});

$magirc->slim->get('/channels/biggest[/{limit}]', function($req, $res, $args) use($magirc) {
    $limit = isset($args['limit']) ? $args['limit'] : 10;
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getChannelBiggest((int) $limit), 'channel'));
});

$magirc->slim->get('/channels/top[/{limit}]', function($req, $res, $args) use($magirc) {
    $limit = isset($args['limit']) ? $args['limit'] : 10;
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getChannelTop((int) $limit), 'channel'));
});

$magirc->slim->get('/channels/activity/{type}', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->service->getChannelGlobalActivity($args['type'], @$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/channels/{chan}', function($req, $res, $args) use($magirc) {
    $magirc->checkPermission('channel', $args['chan']);
    return $res->withJson($magirc->service->getChannel($args['chan']));
});

$magirc->slim->get('/channels/{chan}/users', function($req, $res, $args) use($magirc) {
    $magirc->checkPermission('channel', $args['chan']);
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getChannelUsers($args['chan']), 'nickname'));
});

$magirc->slim->get('/channels/{chan}/activity/{type}', function($req, $res, $args) use($magirc) {
    $magirc->checkPermission('channel', $args['chan']);
    return $res->withJson($magirc->service->getChannelActivity($args['chan'], $args['type'], @$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/channels/{chan}/hourly/{type}', function($req, $res, $args) use($magirc) {
    $magirc->checkPermission('channel', $args['chan']);
    return $res->withJson($magirc->service->getChannelHourlyActivity($args['chan'], $args['type']));
});

$magirc->slim->get('/channels/{chan}/checkstats', function($req, $res, $args) use($magirc) {
    $magirc->checkPermission('channel', $args['chan']);
    return $res->withJson($magirc->service->checkChannelStats($args['chan']));
});

$magirc->slim->get('/channels/{chan}/clients/percent', function($req, $res, $args) use($magirc) {
    $magirc->checkPermission('channel', $args['chan']);
    return $res->withJson($magirc->service->makeClientPieData($magirc->service->getClientStats('channel', $args['chan']), $magirc->service->getUserCount('channel', $args['chan'])));
});

$magirc->slim->get('/channels/{chan}/clients', function($req, $res, $args) use($magirc) {
    $magirc->checkPermission('channel', $args['chan']);
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getClientStats('channel', $args['chan'])));
});

$magirc->slim->get('/channels/{chan}/countries/percent', function($req, $res, $args) use($magirc) {
    $magirc->checkPermission('channel', $args['chan']);
    return $res->withJson($magirc->service->makeCountryPieData($magirc->service->getCountryStats('channel', $args['chan']), $magirc->service->getUserCount('channel', $args['chan'])));
});

$magirc->slim->get('/channels/{chan}/countries', function($req, $res, $args) use($magirc) {
    $magirc->checkPermission('channel', $args['chan']);
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getCountryStats('channel', $args['chan'])));
});

$magirc->slim->get('/users/history', function($req, $res) use($magirc) {
    return $res->withJson($magirc->service->getUserHistory());
});

$magirc->slim->get('/users/top[/{limit}]', function($req, $res, $args) use($magirc) {
    $limit = isset($args['limit']) ? $args['limit'] : 10;
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getUsersTop((int) $limit), 'uname'));
});

$magirc->slim->get('/users/activity/{type}', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->service->getUserGlobalActivity($args['type'], @$_GET['format'] == 'datatables'));
});

$magirc->slim->get('/users/{mode}/{user}', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->service->getUser($args['mode'], $args['user']));
});

$magirc->slim->get('/users/{mode}/{user}/channels', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->service->getUserChannels($args['mode'], $args['user']));
});

$magirc->slim->get('/users/{mode}/{user}/activity[/{chan}]', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getUserActivity($args['mode'], $args['user'], $args['chan'])));
});

$magirc->slim->get('/users/{mode}/{user}/hourly/{type}', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->service->getUserHourlyActivity($args['mode'], $args['user'], null, $args['type']));
});

$magirc->slim->get('/users/{mode}/{user}/hourly/{chan}/{type}', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->service->getUserHourlyActivity($args['mode'], $args['user'], $args['chan'], $args['type']));
});

$magirc->slim->get('/users/{mode}/{user}/checkstats', function($req, $res, $args) use($magirc) {
    return $res->withJson($magirc->service->checkUserStats($args['user'], $args['mode']));
});

$magirc->slim->get('/operators', function($req, $res) use($magirc) {
    return $res->withJson($magirc->arrayForDataTables($magirc->service->getOperatorList(), 'nickname'));
});

// Go! :)
$magirc->slim->run();
