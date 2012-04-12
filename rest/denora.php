<?php
/**
 * MagIRC - Let the magirc begin!
 * RESTful API
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2012 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * @version     0.7.3
 */

include_once('../lib/magirc/version.inc.php');
require_once('../lib/magirc/DB.class.php');
require_once('../lib/magirc/Config.class.php');
require_once('../lib/magirc/denora/Denora.class.php');
require_once('../lib/magirc/Magirc.class.php');
require_once('../lib/slim/Slim.php');

// Initialization
$magirc = new Magirc('denora');
$magirc->slim->contentType('application/json');
$magirc->slim->notFound(function() use($magirc) {
    $magirc->jsonOutput(array('error' => "HTTP 404 Not Found"));
});

// Routing definitions
$magirc->slim->get('/network/status', function() use($magirc) {
	$magirc->jsonOutput($magirc->denora->getCurrentStatus());
});
$magirc->slim->get('/network/max', function() use($magirc) {
	$magirc->jsonOutput($magirc->denora->getMaxValues());
});
$magirc->slim->get('/servers', function() use($magirc) {
	$magirc->jsonOutput($magirc->denora->getServerList(), true, 'server');
});
$magirc->slim->get('/servers/hourlystats', function() use($magirc) {
	$magirc->jsonOutput($magirc->denora->getHourlyStats('servers'));
});
$magirc->slim->get('/servers/:server', function($server) use($magirc) {
	$magirc->jsonOutput($magirc->denora->getServer($server));
});
$magirc->slim->get('/channels', function() use($magirc) {
    $magirc->jsonOutput($magirc->denora->getChannelList(@$_GET['format'] == 'datatables'));
});
$magirc->slim->get('/channels/hourlystats', function() use($magirc) {
    $magirc->jsonOutput($magirc->denora->getHourlyStats('channels'));
});
$magirc->slim->get('/channels/biggest(/:limit)', function($limit = 10) use($magirc) {
	$magirc->jsonOutput($magirc->denora->getChannelBiggest((int) $limit), true, 'channel');
});
$magirc->slim->get('/channels/top(/:limit)', function($limit = 10) use($magirc) {
	$magirc->jsonOutput($magirc->denora->getChannelTop((int) $limit), true, 'channel');
});
$magirc->slim->get('/channels/activity/:type', function($type) use($magirc) {
	$magirc->jsonOutput($magirc->denora->getChannelGlobalActivity($type, @$_GET['format'] == 'datatables'));
});
$magirc->slim->get('/channels/:chan', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->denora->getChannel($chan));
});
$magirc->slim->get('/channels/:chan/users', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->denora->getChannelUsers($chan), true, 'nickname');
});
$magirc->slim->get('/channels/:chan/activity/:type', function($chan, $type) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->denora->getChannelActivity($chan, $type, @$_GET['format'] == 'datatables'));
});
$magirc->slim->get('/channels/:chan/hourly/:type', function($chan, $type) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->denora->getChannelHourlyActivity($chan, $type));
});
$magirc->slim->get('/users/hourlystats', function() use($magirc) {
    $magirc->jsonOutput($magirc->denora->getHourlyStats('users'));
});
$magirc->slim->get('/users/top(/:limit)', function($limit = 10) use($magirc) {
	$magirc->jsonOutput($magirc->denora->getUsersTop((int) $limit), true, 'uname');
});
$magirc->slim->get('/users/activity/:type', function($type) use($magirc) {
	$magirc->jsonOutput($magirc->denora->getUserGlobalActivity($type, @$_GET['format'] == 'datatables'));
});
$magirc->slim->get('/users/:mode/:user', function($mode, $user) use($magirc) {
	$magirc->jsonOutput($magirc->denora->getUser($mode, $user));
});
$magirc->slim->get('/users/:mode/:user/channels', function($mode, $user) use($magirc) {
	$magirc->jsonOutput($magirc->denora->getUserChannels($mode, $user));
});
$magirc->slim->get('/users/:mode/:user/activity(/:chan)', function($mode, $user, $chan = 'global') use($magirc) {
	$magirc->jsonOutput($magirc->denora->getUserActivity($mode, $user, $chan), true);
});
$magirc->slim->get('/users/:mode/:user/hourly/:chan/:type', function($mode, $user, $chan, $type) use($magirc) {
	$magirc->jsonOutput($magirc->denora->getUserHourlyActivity($mode, $user, $chan, $type));
});
$magirc->slim->get('/operators', function() use($magirc) {
	$magirc->jsonOutput($magirc->denora->getOperatorList(), true, 'nickname');
});
$magirc->slim->get('/clientstats(/:chan)', function($chan = null) use($magirc) {
	if ($chan) $magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->denora->getClientStats($chan));
});
$magirc->slim->get('/countrystats(/:chan)', function($chan = null) use($magirc) {
	if ($chan) $magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->denora->getCountryStats($chan));
});

// Go! :)
$magirc->slim->run();

?>