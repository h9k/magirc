<?php
/**
 * MagIRC - Let the magirc begin!
 * RESTful API
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2012 - 2013 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * @version     0.9.0
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

/**
 * Get Current Network Status
 *
 * This will give you current network stats such as opers, channels, users, etc...
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/network/status
 *
 **/
$magirc->slim->get('/network/status', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getCurrentStatus());
});

/**
 * Get Max Network Stats
 *
 * This will give you max network stats such as opers, channels, users, etc...
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/network/max
 *
 **/
$magirc->slim->get('/network/max', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getMaxValues());
});

/**
 * Get the global client stats
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/network/clients
 *
 **/
$magirc->slim->get('/network/clients/percent', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->makeClientPieData($magirc->service->getClientStats(), $magirc->service->getUserCount()));
});

$magirc->slim->get('/network/clients', function($chan = null) use($magirc) {
	$magirc->jsonOutput($magirc->service->getClientStats(), true);
});

/**
 * Get the global country stats
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/network/countries
 *
 **/
$magirc->slim->get('/network/countries/percent', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->makeCountryPieData($magirc->service->getCountryStats(), $magirc->service->getUserCount()));
});

$magirc->slim->get('/network/countries', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getCountryStats(), true);
});

/**
 * Get List of Servers
 *
 * This will give you a list of servers.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/servers
 *
 **/
$magirc->slim->get('/servers', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getServerList(), true, 'server');
});

/**
 * Get Hourly Stats
 *
 * This will give hourly stats whose time is in the form of a unix timestamp.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/servers/
 *
 **/
$magirc->slim->get('/servers/hourlystats', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getHourlyStats('servers'));
});

/**
 * Get A Servers MOTD
 *
 * This will show a servers MOTD
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/servers/s1.<domain>.<tld>
 *
 **/
$magirc->slim->get('/servers/:server', function($server) use($magirc) {
	$magirc->jsonOutput($magirc->service->getServer($server));
});

/**
 * Get the per server client stats
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/servers/<server>/clients
 *
 **/
$magirc->slim->get('/servers/:server/clients/percent', function($server) use($magirc) {
	$magirc->jsonOutput($magirc->service->makeClientPieData($magirc->service->getClientStats('server', $server), $magirc->service->getUserCount('server', $server)));
});

$magirc->slim->get('/servers/:server/clients', function($server) use($magirc) {
	$magirc->jsonOutput($magirc->service->getClientStats('server', $server), true);
});

/**
 * Get the per server country stats
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/servers/<server>/countries
 *
 **/
$magirc->slim->get('/servers/:server/countries/percent', function($server) use($magirc) {
	$magirc->jsonOutput($magirc->service->makeCountryPieData($magirc->service->getCountryStats('server', $server), $magirc->service->getUserCount('server', $server)));
});

$magirc->slim->get('/servers/:server/countries', function($server) use($magirc) {
	$magirc->jsonOutput($magirc->service->getCountryStats('server', $server), true);
});

/**
 * Get List of Channels
 *
 * This will get a list of channels with the current topic, topic author, users,
 * max users, etc...
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels
 *
 **/
$magirc->slim->get('/channels', function() use($magirc) {
    $magirc->jsonOutput($magirc->service->getChannelList(@$_GET['format'] == 'datatables'));
});

/**
 * Get The Hourly Stats for Channels
 *
 * This will get hourly stats for the number of channels on the network. The time
 * is unix timestamp in milliseconds.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/hourlystats
 *
 **/
$magirc->slim->get('/channels/hourlystats', function() use($magirc) {
    $magirc->jsonOutput($magirc->service->getHourlyStats('channels'));
});

/**
 * Get A List of The Biggest Channels
 *
 * This will get a list of the biggest channels on the network. A limit can be
 * defined (eg. 10, 5, 2).
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/biggest/<limit>
 *
 **/
$magirc->slim->get('/channels/biggest(/:limit)', function($limit = 10) use($magirc) {
	$magirc->jsonOutput($magirc->service->getChannelBiggest((int) $limit), true, 'channel');
});

/**
 * Get A List of The Top Channels
 *
 * This will get a list of the top channels on the network. A limit can be
 * defined (eg. 10, 5, 2).
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/top/<limit>
 **/
$magirc->slim->get('/channels/top(/:limit)', function($limit = 10) use($magirc) {
	$magirc->jsonOutput($magirc->service->getChannelTop((int) $limit), true, 'channel');
});

/**
 * Get Channels Acticity Stats
 *
 * This will get a list of channels and their activity stats.
 * Type can be total, monthly, weekly, daily.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/activity/<type>
 **/
$magirc->slim->get('/channels/activity/:type', function($type) use($magirc) {
	$magirc->jsonOutput($magirc->service->getChannelGlobalActivity($type, @$_GET['format'] == 'datatables'));
});

/**
 * Get Stats for a Specific Channel
 *
 * This will show the stats for a specific channel. Stats include name,
 * max users, topic, topic author and more.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/%23<channel>
 *
 **/
$magirc->slim->get('/channels/:chan', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getChannel($chan));
});

/**
 * Get Users in a Specific Channel
 *
 * This will show the stats for a specific channel. Stats include name,
 * max users, topic, topic author and more.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/%23<channel>/users
 *
 **/
$magirc->slim->get('/channels/:chan/users', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getChannelUsers($chan), true, 'nickname');
});

/**
 * Get Activity Stats in a Specific Channel
 *
 * This will show the activity stats for a specific channel.
 * Type can be total, monthly, weekly, daily.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/%23<channel>/activity/<type>
 *
 **/
$magirc->slim->get('/channels/:chan/activity/:type', function($chan, $type) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getChannelActivity($chan, $type, @$_GET['format'] == 'datatables'));
});

/**
 * Get Hourly Activity Stats in a Specific Channel
 *
 * This will show the hourly activity stats for a specific channel.
 * Type can be total, monthly, weekly, daily.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/%23<channel>/hourly/activity/<type>
 *
 **/
$magirc->slim->get('/channels/:chan/hourly/:type', function($chan, $type) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getChannelHourlyActivity($chan, $type));
});

/**
 * Check if channel is being monitored by Chanstats
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/%23<channel>/checkstats
 *
 **/
$magirc->slim->get('/channels/:chan/checkstats', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->checkChannelStats($chan));
});

/**
 * Get the per channel client stats
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/%23<channel>/clients
 *
 **/
$magirc->slim->get('/channels/:chan/clients/percent', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->makeClientPieData($magirc->service->getClientStats('channel', $chan), $magirc->service->getUserCount('channel', $chan)));
});

$magirc->slim->get('/channels/:chan/clients', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getClientStats('channel', $chan), true);
});

/**
 * Get the per channel country stats
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/%23<channel>/countries
 *
 **/
$magirc->slim->get('/channels/:chan/countries/percent', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->makeCountryPieData($magirc->service->getCountryStats('channel', $chan), $magirc->service->getUserCount('channel', $chan)));
});

$magirc->slim->get('/channels/:chan/countries', function($chan) use($magirc) {
	$magirc->checkPermission('channel', $chan);
	$magirc->jsonOutput($magirc->service->getCountryStats('channel', $chan), true);
});


/**
 * Get Hourly User Stats
 *
 * This will show hourly stats for users on the network with unix timestamps in
 * milliseconds.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/users/hourlystats
 *
 **/
$magirc->slim->get('/users/hourlystats', function() use($magirc) {
    $magirc->jsonOutput($magirc->service->getHourlyStats('users'));
});

/**
 * Get Top User Stats
 *
 * This will show hourly stats for users on the network with unix timestamps in
 * milliseconds.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/channels/users/top/<limit>
 *
 **/
$magirc->slim->get('/users/top(/:limit)', function($limit = 10) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUsersTop((int) $limit), true, 'uname');
});

/**
 * Get User Activity Stats
 *
 * This will show the activity stats for a specific user.
 * Type can be total, monthly, weekly, daily.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/users/activity/<type>
 *
 **/
$magirc->slim->get('/users/activity/:type', function($type) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserGlobalActivity($type, @$_GET['format'] == 'datatables'));
});

/**  User Stats Notes
 * For the following funtions there are two modes, stats and nick.
 *
 * - nick tells magirc to treat the user parameter as nickname and look for it in the user table
 * - stats tells magirc to treat the user as stats user and looks for it in the ustats table
 *
 **/

/**
 * Get User Specific Stats
 *
 * This will show user specific stats such as real name, alias, username, nick, etc...
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/users/nick/<nick>
 * Example: http://www.denorastats.org/magirc/rest/service.php/users/stats/<nick>
 *
 **/
$magirc->slim->get('/users/:mode/:user', function($mode, $user) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUser($mode, $user));
});

/**
 * Get User Channels
 *
 * This will show a list of channels in which a given users resides.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/users/nick/<nick>/channels
 * Example: http://www.denorastats.org/magirc/rest/service.php/users/stats/<nick>/channels
 *
 **/
$magirc->slim->get('/users/:mode/:user/channels', function($mode, $user) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserChannels($mode, $user));
});

/**
 * Get Acticity Stats for a Specific User
 *
 * This will show activity stats for a specific user in a given channel.
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/users/nick/<nick>/channels
 * Example: http://www.denorastats.org/magirc/rest/service.php/users/stats/<nick>/channels
 *
 **/
$magirc->slim->get('/users/:mode/:user/activity(/:chan)', function($mode, $user, $chan = null) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserActivity($mode, $user, $chan), true);
});

/**
 * Get Acticity Stats for a Specific User
 *
 * This will show hourly activity stats for a specific user in a given channel
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/users/nick/<nick>/hourly/<channel>/<type>
 * Example: http://www.denorastats.org/magirc/rest/service.php/users/stats/<nick>/hourly/<channel>/<type>
 *
 **/
$magirc->slim->get('/users/:mode/:user/hourly/(:chan/):type', function($mode, $user, $chan = null, $type) use($magirc) {
	$magirc->jsonOutput($magirc->service->getUserHourlyActivity($mode, $user, $chan, $type));
});

/**
 * Check if user is being monitored by Chanstats
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/users/nick/<nick>/checkstats
 * Example: http://www.denorastats.org/magirc/rest/service.php/users/stats/<nick>/checkstats
 *
 **/
$magirc->slim->get('/users/:mode/:user/checkstats', function($mode, $user) use($magirc) {
	$magirc->jsonOutput($magirc->service->checkUserStats($user, $mode));
});

/**
 * Get List of IRC Operators
 *
 * This will show a list of IRC Operators along with the server that they reside
 * on as well as nick, country, level, away, etc...
 *
 * Example: http://www.denorastats.org/magirc/rest/service.php/operators
 *
 **/
$magirc->slim->get('/operators', function() use($magirc) {
	$magirc->jsonOutput($magirc->service->getOperatorList(), true, 'nickname');
});

// Go! :)
$magirc->slim->run();

?>