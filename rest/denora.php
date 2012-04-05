<?php
/**
 * MagIRC - Let the magirc begin!
 * RESTful API
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2012 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * @version     0.7.2
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
$magirc->slim->notFound(function () use ($magirc) {
    echo json_encode(array('error' => "HTTP 404 Not Found"));
});

// Routing
$magirc->slim->get('/network/status', function() use ($magirc) {
	echo json_encode($magirc->denora->getCurrentStatus());
});
$magirc->slim->get('/network/max', function() use ($magirc) {
	echo json_encode($magirc->denora->getMaxValues());
});
$magirc->slim->get('/servers', 'getServers');
$magirc->slim->get('/servers/hourlystats', 'getServerStats');
$magirc->slim->get('/servers/:server', 'getServer');
$magirc->slim->get('/channels', 'getChannels');
$magirc->slim->get('/channels/hourlystats', 'getChannelStats');
$magirc->slim->get('/channels/biggest(/:limit)', 'getChannelsBiggest');
$magirc->slim->get('/channels/top(/:limit)', 'getChannelsTop');
$magirc->slim->get('/channels/activity/:type', 'getChannelGlobalActivity');
$magirc->slim->get('/channels/:chan', 'getChannel');
$magirc->slim->get('/channels/:chan/users', 'getChannelUsers');
$magirc->slim->get('/channels/:chan/activity/:type', 'getChannelActivity');
$magirc->slim->get('/channels/:chan/hourly/:type', 'getChannelHourlyActivity');
$magirc->slim->get('/users', 'getUsers');
$magirc->slim->get('/users/hourlystats', 'getUserStats');
$magirc->slim->get('/users/top(/:limit)', 'getUsersTop');
$magirc->slim->get('/users/activity/:type', 'getUserGlobalActivity');
$magirc->slim->get('/users/:mode/:user', function ($mode, $user) use ($magirc) {
	echo json_encode($magirc->denora->getUser($mode, $user));
});
$magirc->slim->get('/users/:mode/:user/channels', function($mode, $user) use ($magirc) {
	echo json_encode($magirc->denora->getUserChannels($mode, $user));
});
$magirc->slim->get('/users/:mode/:user/activity(/:chan)', function($mode, $user, $chan = 'global') use ($magirc) {
	$data = $magirc->denora->getUserActivity($mode, $user, $chan);
	echo (@$_GET['format'] == "datatables") ? json_encode(array('aaData' => $data)) : json_encode($data);
});
$magirc->slim->get('/users/:mode/:user/hourly/:chan/:type', function($mode, $user, $chan, $type) use($magirc) {
	echo json_encode($magirc->denora->getUserHourlyActivity($mode, $user, $chan, $type));
});
$magirc->slim->get('/operators', 'getOperators');
$magirc->slim->get('/clientstats(/:chan)', 'getClientStats');
$magirc->slim->get('/countrystats(/:chan)', 'getCountryStats');

// Functions
function getServers() {
	global $magirc;
	$data = $magirc->denora->getServerList();
    if (@$_GET['format'] == 'datatables') {
		foreach ($data as $key => $val) {
			$data[$key]["DT_RowId"] = $val["server"];
		}
	}
	echo (@$_GET['format'] == "datatables") ? json_encode(array('aaData' => $data)) : json_encode($data);
}
function getServerStats() {
	global $magirc;
    $data = $magirc->denora->getHourlyStats('serverstats');
	echo json_encode($data);
};
function getServer($server) {
	global $magirc;
    $data = $magirc->denora->getServer($server);
	echo json_encode($data);
};
function getChannels() {
	global $magirc;
    $data = $magirc->denora->getChannelList(@$_GET['format'] == 'datatables');
	echo json_encode($data);
};
function getChannelStats() {
	global $magirc;
    $data = $magirc->denora->getHourlyStats('channelstats');
	echo json_encode($data);
};
function getChannelsBiggest($limit = 10) {
	global $magirc;
    $data = $magirc->denora->getChannelBiggest((int) $limit);
	echo (@$_GET['format'] == "datatables") ? json_encode(array('aaData' => $data)) : json_encode($data);
};
function getChannelsTop($limit = 10) {
	global $magirc;
	$data = $magirc->denora->getChannelTop((int) $limit);
	echo (@$_GET['format'] == "datatables") ? json_encode(array('aaData' => $data)) : json_encode($data);
};
function getChannel($chan) {
	global $magirc;
	switch ($magirc->denora->checkChannel($chan)) {
		case 0: $magirc->slim->notFound();
		case 1: $magirc->slim->halt(403, json_encode(array('error' => "HTTP 403 Access Denied")));
	}
	$data = $magirc->denora->getChannel($chan);
	echo json_encode($data);
}
function getChannelUsers($chan) {
	global $magirc;
	switch ($magirc->denora->checkChannel($chan)) {
		case 0: $magirc->slim->notFound();
		case 1: $magirc->slim->halt(403, json_encode(array('error' => "HTTP 403 Access Denied")));
	}
	$data = $magirc->denora->getChannelUsers($chan);
	echo (@$_GET['format'] == "datatables") ? json_encode(array('aaData' => $data)) : json_encode($data);
}
function getChannelGlobalActivity($type) {
	global $magirc;
	$data = $magirc->denora->getChannelGlobalActivity($type, @$_GET['format'] == 'datatables');
	echo json_encode($data);
}
function getChannelActivity($chan, $type) {
	global $magirc;
	switch ($magirc->denora->checkChannel($chan)) {
		case 0: $magirc->slim->notFound();
		case 1: $magirc->slim->halt(403, json_encode(array('error' => "HTTP 403 Access Denied")));
	}
	$data = $magirc->denora->getChannelActivity($chan, $type, @$_GET['format'] == 'datatables');
	echo json_encode($data);
}
function getChannelHourlyActivity($chan, $type) {
	global $magirc;
	switch ($magirc->denora->checkChannel($chan)) {
		case 0: $magirc->slim->notFound();
		case 1: $magirc->slim->halt(403, json_encode(array('error' => "HTTP 403 Access Denied")));
	}
	$data = $magirc->denora->getChannelHourlyActivity($chan, $type);
	echo json_encode($data);
}
function getUsers() {
	global $magirc;
	$data = $magirc->denora->getUserList();
	echo (@$_GET['format'] == "datatables") ? json_encode(array('aaData' => $data)) : json_encode($data);
};
function getUserStats() {
	global $magirc;
    $data = $magirc->denora->getHourlyStats('stats');
	echo json_encode($data);
};
function getUsersTop($limit = 10) {
	global $magirc;
	$data = $magirc->denora->getUsersTop((int) $limit);
	echo (@$_GET['format'] == "datatables") ? json_encode(array('aaData' => $data)) : json_encode($data);
};
function getUserGlobalActivity($type) {
	global $magirc;
	$data = $magirc->denora->getUserGlobalActivity($type, @$_GET['format'] == 'datatables');
	echo json_encode($data);
}
function getOperators() {
	global $magirc;
	$data = $magirc->denora->getOperatorList();
	if (@$_GET['format'] == 'datatables') {
		foreach ($data as $key => $val) {
			$data[$key]["DT_RowId"] = $val["nick"];
		}
	}
	echo (@$_GET['format'] == "datatables") ? json_encode(array('aaData' => $data)) : json_encode($data);
};
function getClientStats($chan = 'global') {
	global $magirc;
	if ($chan != 'global') {
		switch ($magirc->denora->checkChannel($chan)) {
			case 0: $magirc->slim->notFound();
			case 1: $magirc->slim->halt(403, json_encode(array('error' => "HTTP 403 Access Denied")));
		}
	}
	$data = $magirc->denora->getClientStats($chan);
	echo json_encode($data);
};
function getCountryStats($chan = 'global') {
	global $magirc;
	if ($chan != 'global') {
		switch ($magirc->denora->checkChannel($chan)) {
			case 0: $magirc->slim->notFound();
			case 1: $magirc->slim->halt(403, json_encode(array('error' => "HTTP 403 Access Denied")));
		}
	}
	$data = $magirc->denora->getCountryStats($chan);
	echo json_encode($data);
};

// Go! :)
$magirc->slim->run();

?>
