<?php
/**
 * MagIRC - Let the magirc begin!
 * Denora 2 Anope Migration Script
 *
 * @author      Sebastian Vassiliou <hal9000@denorastats.org>
 * @copyright   2014 Sebastian Vassiliou
 * @link        http://www.magirc.org/
 * @license     GNU GPL Version 3, see http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * @version     0.9.0
 *
 * ABOUT:       This script migrates a Denora 1.4/1.5 database to Anope 2.0.
 * REQUIREMENT: Anope must already be up and running and have the m_mysql, irc2sql and m_chanstats modules enabled!
 *              Please refer to our README.md file for more information about setting up Anope.
 * USAGE:       Configure the two databases below, then run this script from command line: 'php denora2anope.php'
 *              Be patient, migration can take a while!
 */

/**
 * Configuration
 */
define('DENORA_HOSTNAME', '');
define('DENORA_USERNAME', '');
define('DENORA_PASSWORD', '');
define('DENORA_DATABASE', '');

define('ANOPE_HOSTNAME', '');
define('ANOPE_USERNAME', '');
define('ANOPE_PASSWORD', '');
define('ANOPE_DATABASE', '');

/**
 * DO NOT TOUCH FROM HERE ON
 */

if (php_sapi_name() !== 'cli')
	die('Run from commandline!');

echo "Migrating server history...\n";
migrateHistory("serverstats", "servers");
echo "Migrating channel history...\n";
migrateHistory("channelstats", "channels");
echo "Migrating user history...\n";
migrateHistory("stats", "users");
echo "Migrating channel stats...\n";
migrateChannelStats();
echo "Migrating user stats...\n";
migrateUserStats();
echo "Migrating max values...\n";
migrateMaxValues();
echo "DONE\n";

function migrateHistory($from, $to) {
	$denora = new mysqli(DENORA_HOSTNAME, DENORA_USERNAME, DENORA_PASSWORD, DENORA_DATABASE);
	$anope = new mysqli(ANOPE_HOSTNAME, ANOPE_USERNAME, ANOPE_PASSWORD, ANOPE_DATABASE);

	$result = $denora->query("SELECT * FROM $from ORDER BY id ASC", MYSQLI_USE_RESULT);
	while($row = $result->fetch_assoc()) {
		$date = sprintf('%d-%02d-%02d', $row['year'], $row['month'], $row['day']);
		for ($i = 0; $i < 24; $i++) {
			$datetime =  sprintf('%s %02d:00:00', $date, $i);
			$value = $row['time_'.$i];
			if ($value > 0) {
				$query = "INSERT INTO anope_history (`datetime`, $to) VALUES ('$datetime', $value) ON DUPLICATE KEY UPDATE $to = $value";
				if (!$anope->query($query)) {
					die('FAILURE: '.$query);
				}
			}
		}
	}
	$result->close();
	$denora->close();
	$anope->close();
}

function migrateChannelStats() {
	$denora = new mysqli(DENORA_HOSTNAME, DENORA_USERNAME, DENORA_PASSWORD, DENORA_DATABASE);
	$anope = new mysqli(ANOPE_HOSTNAME, ANOPE_USERNAME, ANOPE_PASSWORD, ANOPE_DATABASE);

	$result = $denora->query("SELECT * FROM cstats ORDER BY chan, `type`", MYSQLI_USE_RESULT);
	while ($row = $result->fetch_assoc()){
		$query = sprintf("INSERT INTO anope_chanstats (chan, `type`, letters, words, line, actions, smileys_other, kicks, modes, topics,
		time0, time1, time2, time3, time4, time5, time6, time7, time8, time9, time10, time11,
		time12, time13, time14, time15, time16, time17, time18, time19, time20, time21, time22, time23)
		VALUES('%s', '%s', %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d)
		ON DUPLICATE KEY UPDATE letters = letters + %d, words = words + %d, line = line + %d, actions = actions + %d, smileys_other = smileys_other + %d,
		kicks = kicks + %d, modes = modes + %d, topics = topics + %d,
		time0 = time0 + %d, time1 = time1 + %d, time2 = time2 + %d, time3 = time3 + %d, time4 = time4 + %d, time5 = time5 + %d,
		time6 = time6 + %d, time7 = time7 + %d, time8 = time8 + %d, time9 = time9 + %d, time10 = time10 + %d, time11 = time11 + %d,
		time12 = time12 + %d, time13 = time13 + %d, time14 = time14 + %d, time15 = time15 + %d, time16 = time16 + %d, time17 = time17 + %d,
		time18 = time18 + %d, time19 = time19 + %d, time20 = time20 + %d, time21 = time21 + %d, time22 = time22 + %d, time23 = time23 + %d
		",
			$row['chan'], getAnopeChanstatsType($row['type']),
			$row['letters'], $row['words'], $row['line'], $row['actions'], $row['smileys'], $row['kicks'], $row['modes'], $row['topics'],
			$row['time0'], $row['time1'], $row['time2'], $row['time3'], $row['time4'], $row['time5'], $row['time6'], $row['time7'], $row['time8'], $row['time9'], $row['time10'], $row['time11'],
			$row['time12'], $row['time13'], $row['time14'], $row['time15'], $row['time16'], $row['time17'], $row['time18'], $row['time19'], $row['time20'], $row['time21'], $row['time22'], $row['time23'],
			$row['letters'], $row['words'], $row['line'], $row['actions'], $row['smileys'], $row['kicks'], $row['modes'], $row['topics'],
			$row['time0'], $row['time1'], $row['time2'], $row['time3'], $row['time4'], $row['time5'], $row['time6'], $row['time7'], $row['time8'], $row['time9'], $row['time10'], $row['time11'],
			$row['time12'], $row['time13'], $row['time14'], $row['time15'], $row['time16'], $row['time17'], $row['time18'], $row['time19'], $row['time20'], $row['time21'], $row['time22'], $row['time23']
		);
		if (!$anope->query($query)) {
			die('FAILURE: '.$query);
		}
	}
	$result->close();
	$denora->close();
	$anope->close();
}

function migrateUserStats() {
	$denora = new mysqli(DENORA_HOSTNAME, DENORA_USERNAME, DENORA_PASSWORD, DENORA_DATABASE);
	$anope = new mysqli(ANOPE_HOSTNAME, ANOPE_USERNAME, ANOPE_PASSWORD, ANOPE_DATABASE);

	$result = $denora->query("SELECT * FROM ustats ORDER BY chan, `type`", MYSQLI_USE_RESULT);
	while ($row = $result->fetch_assoc()){
		$account = getAccount($row['uname']);
		if (!$account)
			continue;

		if (!checkAccount($account))
			continue;

		$query = sprintf("INSERT INTO anope_chanstats (chan, nick, `type`, letters, words, line, actions, smileys_other, kicks, modes, topics,
		time0, time1, time2, time3, time4, time5, time6, time7, time8, time9, time10, time11,
		time12, time13, time14, time15, time16, time17, time18, time19, time20, time21, time22, time23)
		VALUES('%s', '%s', '%s', %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d)
		ON DUPLICATE KEY UPDATE letters = letters + %d, words = words + %d, line = line + %d, actions = actions + %d, smileys_other = smileys_other + %d,
		kicks = kicks + %d, modes = modes + %d, topics = topics + %d,
		time0 = time0 + %d, time1 = time1 + %d, time2 = time2 + %d, time3 = time3 + %d, time4 = time4 + %d, time5 = time5 + %d,
		time6 = time6 + %d, time7 = time7 + %d, time8 = time8 + %d, time9 = time9 + %d, time10 = time10 + %d, time11 = time11 + %d,
		time12 = time12 + %d, time13 = time13 + %d, time14 = time14 + %d, time15 = time15 + %d, time16 = time16 + %d, time17 = time17 + %d,
		time18 = time18 + %d, time19 = time19 + %d, time20 = time20 + %d, time21 = time21 + %d, time22 = time22 + %d, time23 = time23 + %d
		",
			($row['chan'] == 'global') ? '' : $row['chan'], $account, getAnopeChanstatsType($row['type']),
			$row['letters'], $row['words'], $row['line'], $row['actions'], $row['smileys'], $row['kicks'], $row['modes'], $row['topics'],
			$row['time0'], $row['time1'], $row['time2'], $row['time3'], $row['time4'], $row['time5'], $row['time6'], $row['time7'], $row['time8'], $row['time9'], $row['time10'], $row['time11'],
			$row['time12'], $row['time13'], $row['time14'], $row['time15'], $row['time16'], $row['time17'], $row['time18'], $row['time19'], $row['time20'], $row['time21'], $row['time22'], $row['time23'],
			$row['letters'], $row['words'], $row['line'], $row['actions'], $row['smileys'], $row['kicks'], $row['modes'], $row['topics'],
			$row['time0'], $row['time1'], $row['time2'], $row['time3'], $row['time4'], $row['time5'], $row['time6'], $row['time7'], $row['time8'], $row['time9'], $row['time10'], $row['time11'],
			$row['time12'], $row['time13'], $row['time14'], $row['time15'], $row['time16'], $row['time17'], $row['time18'], $row['time19'], $row['time20'], $row['time21'], $row['time22'], $row['time23']
		);
		if (!$anope->query($query)) {
			die('FAILURE: '.$query);
		}
	}
	$result->close();
	$denora->close();
	$anope->close();
}

function migrateMaxValues() {
	$denora = new mysqli(DENORA_HOSTNAME, DENORA_USERNAME, DENORA_PASSWORD, DENORA_DATABASE);
	$anope = new mysqli(ANOPE_HOSTNAME, ANOPE_USERNAME, ANOPE_PASSWORD, ANOPE_DATABASE);

	$result = $denora->query("SELECT * FROM `maxvalues`", MYSQLI_USE_RESULT);
	while ($row = $result->fetch_assoc()){
		if ($row['type'] == 'opers') {
			$row['type'] = 'operators';
		}
		$query = sprintf("UPDATE anope_maxusage SET `count` = %d, `datetime` = '%s' WHERE `type` = '%s' AND `count` < %d",
			$row['val'], $row['time'], $row['type'], $row['val']);
		$anope->query($query);
	}
	$result->close();
	$denora->close();
	$anope->close();
}

function getAccount($uname) {
	$denora = new mysqli(DENORA_HOSTNAME, DENORA_USERNAME, DENORA_PASSWORD, DENORA_DATABASE);
	$query = sprintf("SELECT u.account FROM `user` AS u JOIN aliases AS a ON a.nick = u.nick WHERE a.uname = '%s' LIMIT 1", $uname);
	if ($result = $denora->query($query, MYSQLI_USE_RESULT)) {
		$row = $result->fetch_assoc();
		$result->close();
		$denora->close();
		return $row['account'];
	}
	$denora->close();
	return null;
}

function checkAccount($account) {
	$anope = new mysqli(ANOPE_HOSTNAME, ANOPE_USERNAME, ANOPE_PASSWORD, ANOPE_DATABASE);
	$query = "SELECT * FROM anope_user WHERE account = '{$account}'";
	if ($result = $anope->query($query)) {
		$row = $result->fetch_assoc();
		$result->close();
		if ($row) {
			$anope->close();
			return true;
		}
	}
	$anope->close();
	return false;
}

function getAnopeChanstatsType($type) {
	switch ($type) {
		case 1:
			return 'daily';
		case 2:
			return 'weekly';
		case 3:
			return 'monthly';
	}
	return 'total';
}
