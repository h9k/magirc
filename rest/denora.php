<?php

include_once('../lib/magirc/version.inc.php');
require_once('../lib/magirc/DB.class.php');
require_once('../lib/magirc/Config.class.php');
require_once('../lib/magirc/denora/Denora.class.php');
require_once('../lib/magirc/Magirc.class.php');
require_once('../lib/slim/Slim.php');

// Initialization
$magirc = new Magirc('denora');
$app = new Slim();
#$app->contentType('application/json');
$app->notFound(function () use ($app) {
    echo json_encode(array('error' => "HTTP 404 Not Found"));
});

// Routing
$app->get('/network/status', 'getNetworkStatus');
$app->get('/network/max', 'getNetworkMax');
$app->get('/servers', 'getServers');
$app->get('/servers/hourlystats', 'getServerStats');
$app->get('/servers/:server', 'getServer');
$app->get('/channels', 'getChannels');
$app->get('/channels/hourlystats', 'getChannelStats');
$app->get('/channels/biggest(/:limit)', 'getChannelsBiggest');
$app->get('/channels/top(/:limit)', 'getChannelsTop');
$app->get('/channels/activity/:type', 'getChannelGlobalActivity');
$app->get('/channels/:chan', 'getChannel');
$app->get('/channels/:chan/users', 'getChannelUsers');
$app->get('/channels/:chan/activity/:type', 'getChannelActivity');
$app->get('/channels/:chan/hourly/:type', 'getChannelHourlyActivity');
$app->get('/users', 'getUsers');
$app->get('/users/hourlystats', 'getUserStats');
$app->get('/users/top(/:limit)', 'getUsersTop');
$app->get('/users/activity/:type', 'getUserGlobalActivity');
$app->get('/operators', 'getOperators');
$app->get('/clientstats(/:chan)', 'getClientStats');
$app->get('/countrystats(/:chan)', 'getCountryStats');

// Functions
function getNetworkStatus() {
	global $magirc;
    $data = $magirc->denora->getCurrentStatus();
	echo json_encode($data);
}
function getNetworkMax() {
	global $magirc;
    $data = $magirc->denora->getMaxValues();
	echo json_encode($data);
}
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
	$data = $magirc->denora->getChannel($chan);
	echo json_encode($data);
}
function getChannelUsers($chan) {
	global $magirc;
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
	$data = $magirc->denora->getChannelActivity($chan, $type, @$_GET['format'] == 'datatables');
	echo json_encode($data);
}
function getChannelHourlyActivity($chan, $type) {
	global $magirc;
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
	$data = $magirc->denora->getClientStats($chan);
	echo json_encode($data);
};
function getCountryStats($chan = 'global') {
	global $magirc;
	$data = $magirc->denora->getCountryStats($chan);
	echo json_encode($data);
};

// Go! :)
$app->run();

unset($magirc, $app);

?>
