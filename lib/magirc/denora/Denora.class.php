<?php

class Denora {

	private $db;
	private $cfg;

	function __construct() {
		// Check the database configuration
		$error = false;
		$config_file = PATH_ROOT . 'conf/denora.cfg.php';
		if (file_exists($config_file)) {
			include($config_file);
		} else {
			die($config_file);
			$error = true;
		}
		if ($error || !isset($db)) {
			die('<strong>MagIRC</strong> is not properly configured<br />Please configure the Denora database in the <a href="admin/">Admin Panel</a>');
		}
		// Get the ircd
		$ircd_file = PATH_ROOT . "lib/magirc/denora/protocol/" . IRCD . ".inc.php";
		if (file_exists($ircd_file)) {
			require_once($ircd_file);
		} else {
			die('<strong>MagIRC</strong> is not properly configured<br />Please configure the ircd in the <a href="admin/">Admin Panel</a>');
		}
		// Load the required classes
		$this->db = new DB();
		$this->db->connect("mysql:dbname={$db['database']};host={$db['hostname']}", $db['username'], $db['password']) || die('Error opening Denora database<br />' . $this->error);
		$this->cfg = new Config();
		require_once(__DIR__ . '/Objects.class.php');
	}

	/**
	 * Returns the current status
	 * @return array of arrays (int val, int time)
	 */
	function getCurrentStatus() {
		$query = "SELECT type, val, FROM_UNIXTIME(time) AS time FROM current";
		$this->db->query($query, SQL_ALL, SQL_ASSOC);
		$result = $this->db->record;
		$data = array();
		foreach ($result as $row) {
			$data[$row["type"]] = array('val' => (int) $row["val"], 'time' => $row['time']);
		}
		return $data;
	}

	/**
	 * Returns the ma values
	 * @return array of arrays (int val, int time)
	 */
	function getMaxValues() {
		$query = "SELECT type, val, time FROM maxvalues";
		$this->db->query($query, SQL_ALL, SQL_ASSOC);
		$result = $this->db->record;
		$data = array();
		foreach ($result as $row) {
			$data[$row["type"]] = array('val' => (int) $row["val"], 'time' => $row['time']);
		}
		return $data;
	}

	/**
	 * Return the mode formatted for SQL
	 * Example: o -> mode_lo, C -> mode_uc
	 * @param string $mode Mode
	 * @return string SQL Mode
	 */
	public static function getSqlMode($mode) {
		if (!$mode) {
			return null;
		} elseif (strtoupper($mode) === $mode) {
			return "mode_u" . strtolower($mode);
		} else {
			return "mode_l" . strtolower($mode);
		}
	}
	
	/**
	 * Return the mode data formatted for SQL
	 * Example: o -> mode_lo_data, C -> mode_uc_data
	 * @param string $mode Mode
	 * @return string SQL Mode data
	 */
	public static function getSqlModeData($mode) {
		$sql_mode = Denora::getSqlMode($mode);
		return $sql_mode ? $sql_mode . "_data" : null;
	}

	/**
	 * Get the global or channel-specific user count
	 * @param string $chan Channel (null for global)
	 * @return int User count
	 */
	private function getUserCount($chan = null) {
		$query = "SELECT COUNT(*) FROM user
			JOIN server ON server.servid = user.servid";
		if ($chan) {
			$query .= " JOIN ison ON ison.nickid = user.nickid
			JOIN chan ON chan.chanid = ison.chanid
			WHERE LOWER(chan.channel)=LOWER(:chan) AND user.online = 'Y'";
		} else {
			$query .= " WHERE user.online = 'Y'";
		}
		if ($this->cfg->getParam('hide_ulined')) $query .= " AND server.uline = 0";
		if (Protocol::services_protection_mode) {
			$query .= sprintf(" AND user.%s='N'", Denora::getSqlMode(Protocol::services_protection_mode));
		}
		$stmt = $this->db->prepare($query);
		if ($chan != 'global') $stmt->bindParam(':chan', $chan, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_COLUMN);
	}

	/**
	 * Get CTCP/GeoIP statistics for use by pie charts
	 * @param string $type Type ('clients': client stats, 'countries': country stats)
	 * @param string $chan Channel (null for global)
	 * @return array Data
	 */
	private function getPieStats($type, $chan = null) {
		$type = ($type == 'clients') ? 'ctcpversion' : 'country';
		$query = "SELECT user.$type AS name, COUNT(*) AS count FROM user
			JOIN server ON server.servid = user.servid";
		if ($chan) {
			$query .= " JOIN ison ON ison.nickid = user.nickid
				JOIN chan ON chan.chanid = ison.chanid
				WHERE LOWER(chan.channel)=LOWER(:chan)
				AND user.online='Y'";
		} else {
			$query .= " WHERE user.online='Y'";
		}
		if ($this->cfg->getParam('hide_ulined')) $query .= " AND server.uline = 0";
		if (Protocol::services_protection_mode) {
			$query .= sprintf(" AND user.%s='N'", Denora::getSqlMode(Protocol::services_protection_mode));
		}
		$query .= " GROUP by user.$type ORDER BY count DESC";
		$stmt = $this->db->prepare($query);
		if ($chan != 'global') $stmt->bindParam(':chan', $chan, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Get CTCP client statistics
	 * @param string $chan Channel (null for global)
	 * @return array Data
	 */
	function getClientStats($chan = null) {
		return $this->makeData($this->getPieStats('clients', $chan), $this->getUserCount($chan));
	}

	/**
	 * Get GeoIP country statistics
	 * @param string $chan Channel (null for global)
	 * @return array Data
	 */
	function getCountryStats($chan = null) {
		return $this->makeData($this->getPieStats('countries', $chan), $this->getUserCount($chan));
	}

	/**
	 * Prepare data for use by pie charts
	 * @param array $result Array of data
	 * @param type $sum user count
	 * @return array of arrays (string 'name', double 'value')
	 */
	private function makeData($result, $sum) {
		$data = array();
		$unknown = 0;
		$other = 0;
		foreach ($result as $val) {
			$percent = round($val["count"] / $sum * 100, 2);
			if ($percent < 2) {
				$other += $val["count"];
			} elseif ($val["name"] == null || $val["name"] == "Unknown") {
				$unknown += $val["count"];
			} else {
				$data[] = array($val["name"], $percent);
			}
		}
		if ($unknown > 0) {
			$data[] = array("Unknown", round($unknown / $sum * 100, 2));
		}
		if ($other > 0) {
			$data[] = array("Other", round($other / $sum * 100, 2));
		}
		return $data;
	}

	/**
	 * Get hourly user/channel/server stats
	 * @param string $table 'users', 'channels', 'servers'
	 * @return array of arrays (int milliseconds, int value)
	 */
	function getHourlyStats($table) {
		switch ($table) {
			case 'users': $table = 'stats'; break;
			case 'channels': $table = 'channelstats'; break;
			case 'servers': $table = 'serverstats'; break;
			default: return null;
		}
		$query = "SELECT * FROM {$table} ORDER BY year ASC, month ASC, day ASC";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$data = array();
		foreach ($result as $val) {
			$date = "{$val['year']}-{$val['month']}-{$val['day']}";
			for ($i = 0; $i < 24; $i++) {
				$data[] = array(strtotime("{$date} {$i}:00:00") * 1000, $val["time_" . $i] ? (int) $val["time_" . $i] : null);
			}
		}
		return $data;
	}

	/**
	 * Gets a list of servers
	 * @return array of Server
	 */
	function getServerList() {
		$sWhere = "";
		$hide_servers = $this->cfg->getParam('hide_servers');
		if ($hide_servers) {
			$hide_servers = explode(",", $hide_servers);
			foreach ($hide_servers as $key => $server) {
				$hide_servers[$key] = $this->db->escape(trim($server));
			}
			$sWhere .= sprintf("WHERE server NOT IN('%s')", implode("','", $hide_servers));
		}
		if ($this->cfg->getParam('hide_ulined')) {
			$sWhere .= $sWhere ? " AND uline = 0" : "WHERE uline = 0";
		}
		$query = "SELECT server, online, comment AS description, currentusers AS users, opers FROM server $sWhere";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_CLASS, 'Server');
	}

	/**
	 * Gets a server
	 * @param string $server Server name
	 * @return Server
	 */
	function getServer($server) {
		$query = "SELECT server, online, comment AS description, connecttime AS connect_time, lastsplit AS split_time, version,
			uptime, motd, currentusers AS users, maxusers AS users_max, FROM_UNIXTIME(maxusertime) AS users_max_time, ping, highestping AS ping_max,
			FROM_UNIXTIME(maxpingtime) AS ping_max_time, opers, maxopers AS opers_max, FROM_UNIXTIME(maxopertime) AS opers_max_time
			FROM server WHERE server = :server";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':server', $server, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchObject('Server');
	}

	/**
	 * Get the list of Operators currently online
	 * @return array of User
	 */
	function getOperatorList() {
		$query = sprintf("SELECT u.nick AS nickname, u.realname, u.hostname, u.hiddenhostname AS hostname_cloaked, u.swhois,
			u.username, u.connecttime AS connect_time, u.server, u.away, u.awaymsg AS away_msg, u.ctcpversion AS client, u.online,
			u.lastquit AS quit_time, u.lastquitmsg AS quit_msg, u.countrycode AS country_code, u.country, s.uline AS service,
			%s FROM user u LEFT JOIN server s ON s.servid = u.servid WHERE",
				implode(',', array_map(array('Denora', 'getSqlMode'), str_split(Protocol::user_modes))));
		if (Protocol::ircd == "unreal32") {
			$query .= " (u.mode_un = 'Y' OR u.mode_ua = 'Y' OR u.mode_la = 'Y' OR u.mode_uc = 'Y' OR u.mode_lo = 'Y')";
		} else {
			$query .= " u.mode_lo = 'Y'";
		}
		$query .= " AND u.online = 'Y'";
		if (Protocol::oper_hidden_mode) $query .= " AND u." . Denora::getSqlMode(Protocol::oper_hidden_mode) . " = 'N'";
		if (Protocol::services_protection_mode) $query .= " AND u." . Denora::getSqlMode(Protocol::services_protection_mode) . " = 'N'";
		$query .= " AND u.server = s.server";
		if ($this->cfg->getParam('hide_ulined')) $query .= " AND s.uline = '0'";
		$query .= " ORDER BY u.nick ASC";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
	}

	/**
	 * Gets the list of current channels
	 * @param boolean $datatables Set true to enable server-side datatables functionality
	 * @return array of Channel 
	 */
	function getChannelList($datatables = false) {
		$aaData = array();
		$secret_mode = Protocol::chan_secret_mode;
		$private_mode = Protocol::chan_private_mode;

		$sWhere = "currentusers > 0";
		if ($secret_mode) {
			$sWhere .= sprintf(" AND %s='N'", Denora::getSqlMode($secret_mode));
		}
		if ($private_mode) {
			$sWhere .= sprintf(" AND %s='N'", Denora::getSqlMode($private_mode));
		}
		$hide_channels = $this->cfg->getParam('hide_chans');
		if ($hide_channels) {
			$hide_channels = explode(",", $hide_channels);
			foreach ($hide_channels as $key => $channel) {
				$hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
			}
			$sWhere .= sprintf("%s LOWER(channel) NOT IN(%s)", $sWhere ? " AND " : "WHERE ", implode(",", $hide_channels));
		}
		
		$sQuery = sprintf("SELECT SQL_CALC_FOUND_ROWS channel, currentusers AS users, maxusers AS users_max, maxusertime AS users_max_time,
			topic, topicauthor AS topic_author, topictime AS topic_time, kickcount AS kicks, %s, %s FROM chan WHERE %s",
				implode(',', array_map(array('Denora', 'getSqlMode'), str_split(Protocol::chan_modes))),
				implode(',', array_map(array('Denora', 'getSqlModeData'), str_split(Protocol::chan_modes_data))), $sWhere);
		if ($datatables) {
			$iTotal = $this->db->datatablesTotal($sQuery);
			$sFiltering = $this->db->datatablesFiltering(array('channel', 'topic'));
			$sOrdering = $this->db->datatablesOrdering(array('channel', 'currentusers', 'maxusers'));
			$sPaging = $this->db->datatablesPaging();
			$sQuery .= sprintf(" %s %s %s", $sFiltering ? "AND " . $sFiltering : "", $sOrdering, $sPaging);
		} else {
			$sQuery .= " ORDER BY `channel` ASC";
		}
		$ps = $this->db->prepare($sQuery);
		$ps->execute();
		$aaData = $ps->fetchAll(PDO::FETCH_CLASS, 'Channel');
		if ($datatables) {
			$iFilteredTotal = $this->db->foundRows();
			return $this->db->datatablesOutput($iTotal, $iFilteredTotal, $aaData);
		}
		return $aaData;
	}

	/**
	 * Get the biggest current channels
	 * @param int $limit
	 * @return array of Channel 
	 */
	function getChannelBiggest($limit = 10) {
		$secret_mode = Protocol::chan_secret_mode;
		$private_mode = Protocol::chan_private_mode;
		$query = "SELECT channel, currentusers AS users, maxusers AS users_max, maxusertime AS users_max_time FROM chan WHERE currentusers > 0";
		if ($secret_mode) {
			$query .= sprintf(" AND %s='N'", Denora::getSqlMode($secret_mode));
		}
		if ($private_mode) {
			$query .= sprintf(" AND %s='N'", Denora::getSqlMode($private_mode));
		}
		$hide_chans = explode(",", $this->cfg->getParam('hide_chans'));
		for ($i = 0; $i < count($hide_chans); $i++) {
			$query .= " AND LOWER(channel) NOT LIKE " . $this->db->escape(strtolower($hide_chans[$i]));
		}
		$query .= " ORDER BY currentusers DESC LIMIT :limit";
		$ps = $this->db->prepare($query);
		$ps->bindParam(':limit', $limit, PDO::PARAM_INT);
		$ps->execute();
		return $ps->fetchAll(PDO::FETCH_CLASS, 'Channel');
	}
	
	/**
	 * Get the most active current channels
	 * @param int $limit
	 * @return array of channel stats 
	 */
	function getChannelTop($limit = 10) {
		$secret_mode = Protocol::chan_secret_mode;
		$private_mode = Protocol::chan_private_mode;
		$query = "SELECT chan AS channel, line AS 'lines' FROM cstats, chan WHERE BINARY LOWER(cstats.chan)=LOWER(chan.channel) AND cstats.type=1 AND cstats.line >= 1";
		if ($secret_mode) {
			$query .= sprintf(" AND chan.%s='N'", Denora::getSqlMode($secret_mode));
		}
		if ($private_mode) {
			$query .= sprintf(" AND chan.%s='N'", Denora::getSqlMode($private_mode));
		}
		$hide_chans = explode(",", $this->cfg->getParam('hide_chans'));
		for ($i = 0; $i < count($hide_chans); $i++) {
			$query .= " AND cstats.chan NOT LIKE " . $this->db->escape(strtolower($hide_chans[$i]));
		}
		$query .= " ORDER BY cstats.line DESC LIMIT :limit";
		$ps = $this->db->prepare($query);
		$ps->bindParam(':limit', $limit, PDO::PARAM_INT);
		$ps->execute();
		return $ps->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Get the most active current users
	 * @param int $limit
	 * @return array of user stats 
	 */
	function getUsersTop($limit = 10) {
		$aaData = array();
		$ps = $this->db->prepare("SELECT uname, line AS 'lines' FROM ustats WHERE type = 1 AND chan='global' AND line >= 1 ORDER BY line DESC LIMIT :limit");
		$ps->bindParam(':limit', $limit, PDO::PARAM_INT);
		$ps->execute();
		$data = $ps->fetchAll(PDO::FETCH_ASSOC);
		foreach ($data as $row) {
			$user = $this->getUser('stats', $row['uname']);
			if (!$user) $user = new User();
			$user->uname = $row['uname'];
			$user->lines = $row['lines'];
			$aaData[] = $user;
		}
		return $aaData;
	}

	/**
	 * Get the specified channel
	 * @param string $chan Channel
	 * @return Channel 
	 */
	function getChannel($chan) {
		$sQuery = sprintf("SELECT channel, currentusers AS users, maxusers AS users_max, maxusertime AS users_max_time,
			topic, topicauthor AS topic_author, topictime AS topic_time, kickcount AS kicks, %s, %s
			FROM chan WHERE BINARY LOWER(channel) = LOWER(:chan)",
				implode(',', array_map(array('Denora', 'getSqlMode'), str_split(Protocol::chan_modes))),
				implode(',', array_map(array('Denora', 'getSqlModeData'), str_split(Protocol::chan_modes_data))));
		$ps = $this->db->prepare($sQuery);
		$ps->bindParam(':chan', $chan, PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetchObject('Channel');
	}

	/**
	 * Checks if given channel can be displayed
	 * @param string $chan
	 * @return int code (200: OK, 404: not existing, 403: denied)
	 */
	function checkChannel($chan) {
		$noshow = array();
		$no = explode(",", $this->cfg->getParam('hide_chans'));
		for ($i = 0; $i < count($no); $i++) {
			$noshow[$i] = strtolower($no[$i]);
		}
		if (in_array(strtolower($chan), $noshow))
			return 403;

		$stmt = $this->db->prepare("SELECT * FROM `chan` WHERE BINARY LOWER(`channel`) = LOWER(:channel)");
		$stmt->bindParam(':channel', $chan, SQL_STR);
		$stmt->execute();
		$data = $stmt->fetch();

		if (!$data) {
			return 404;
		} else {
			if ($this->cfg->getParam('block_spchans')) {
				if (Protocol::chan_secret_mode && @$data[Denora::getSqlMode(Protocol::chan_secret_mode)] == 'Y' ) return 403;
				if (Protocol::chan_private_mode && @$data[Denora::getSqlMode(Protocol::chan_private_mode)] == 'Y' ) return 403;
			}
			if (@$data['mode_li'] == "Y" || @$data['mode_lk'] == "Y" || @$data['mode_uo'] == "Y") {
				return 403;
			} else {
				return 200;
			}
		}
	}

	/**
	 * Get the users currently in the specified channel
	 * @todo implement server-side datatables support
	 * @param string $chan Channel
	 * @return array of User 
	 */
	function getChannelUsers($chan) {
		if ($this->checkChannel($chan) != 200) {
			return null;
		}
		$query = "SELECT u.nick AS nickname, u.realname, u.hostname, u.hiddenhostname AS hostname_cloaked, u.swhois,
			u.username, u.connecttime AS connect_time, u.server, u.away, u.awaymsg AS away_msg, u.ctcpversion AS client, u.online,
			u.lastquit AS quit_time, u.lastquitmsg AS quit_msg, u.countrycode AS country_code, u.country, s.uline AS service,
			i.mode_lq AS cmode_lq, i.mode_la AS cmode_la, i.mode_lo AS cmode_lo, i.mode_lh AS cmode_lh, i.mode_lv AS cmode_lv
		FROM ison i, chan c, user u, server s
		WHERE LOWER(c.channel) = LOWER(:channel)
			AND i.chanid = c.chanid
			AND i.nickid = u.nickid
			AND u.server = s.server
		ORDER BY u.nick ASC";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':channel', $chan, SQL_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
	}

	function getChannelGlobalActivity($type, $datatables = false) {
		$aaData = array();
		$secret_mode = Protocol::chan_secret_mode;
		$private_mode = Protocol::chan_private_mode;

		$sWhere = "cstats.letters>0";
		if ($secret_mode) {
			$sWhere .= sprintf(" AND chan.%s='N'", Denora::getSqlMode($secret_mode));
		}
		if ($private_mode) {
			$sWhere .= sprintf(" AND chan.%s='N'", Denora::getSqlMode($private_mode));
		}
		$hide_channels = $this->cfg->getParam('hide_chans');
		if ($hide_channels) {
			$hide_channels = explode(",", $hide_channels);
			foreach ($hide_channels as $key => $channel) {
				$hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
			}
			$sWhere .= sprintf(" AND LOWER(cstats.chan) NOT IN(%s)", implode(',', $hide_channels));
		}

		$sQuery = sprintf("SELECT SQL_CALC_FOUND_ROWS chan AS name,letters,words,line AS 'lines',actions,smileys,kicks,modes,topics FROM cstats
			 JOIN chan ON BINARY LOWER(cstats.chan)=LOWER(chan.channel) WHERE cstats.type=:type AND %s", $sWhere);
		if ($datatables) {
			$iTotal = $this->db->datatablesTotal($sQuery, array(':type' => $type));
			$sFiltering = $this->db->datatablesFiltering(array('cstats.chan', 'chan.topic'));
			$sOrdering = $this->db->datatablesOrdering(array('chan', 'letters', 'words', 'line', 'actions', 'smileys', 'kicks', 'modes', 'topics'));
			$sPaging = $this->db->datatablesPaging();
			$sQuery .= sprintf("%s %s %s", $sFiltering ? " AND " . $sFiltering : "", $sOrdering, $sPaging);
		}
		$ps = $this->db->prepare($sQuery);
		$ps->bindParam(':type', $type, PDO::PARAM_INT);
		$ps->execute();
		foreach ($ps->fetchAll(PDO::FETCH_ASSOC) as $row) {
			if ($datatables) {
				$row["DT_RowId"] = $row['name'];
			}
			$aaData[] = $row;
		}
		if ($datatables) {
			$iFilteredTotal = $this->db->foundRows();
			return $this->db->datatablesOutput($iTotal, $iFilteredTotal, $aaData);
		}
		return $aaData;
	}

	function getChannelActivity($chan, $type, $datatables = false) {
		$aaData = array();
		$sQuery = "SELECT SQL_CALC_FOUND_ROWS uname,letters,words,line AS 'lines',actions,smileys,kicks,modes,topics FROM ustats WHERE chan=:channel AND type=:type AND letters > 0 ";
		if ($datatables) {
			$iTotal = $this->db->datatablesTotal($sQuery, array(':type' => $type, ':channel' => $chan));
			$sFiltering = $this->db->datatablesFiltering(array('uname'));
			$sOrdering = $this->db->datatablesOrdering(array('uname', 'letters', 'words', 'line', 'actions', 'smileys', 'kicks', 'modes', 'topics'));
			$sPaging = $this->db->datatablesPaging();
			$sQuery .= sprintf("%s %s %s", $sFiltering ? " AND " . $sFiltering : "", $sOrdering, $sPaging);
		}
		$ps = $this->db->prepare($sQuery);
		$ps->bindParam(':type', $type, PDO::PARAM_INT);
		$ps->bindParam(':channel', $chan, PDO::PARAM_STR);
		$ps->execute();
		foreach ($ps->fetchAll(PDO::FETCH_ASSOC) as $row) {
			if ($datatables) {
				$row["DT_RowId"] = $row['uname'];
			}
			// Get country code and online status
			$user = $this->getUser('stats', $row['uname']);
			if (!$user) $user = new User();
			foreach ($row as $key => $val) {
				$user->$key = $val;
			}
			$aaData[] = $user;
		}
		if ($datatables) {
			$iFilteredTotal = $this->db->foundRows();
			return $this->db->datatablesOutput($iTotal, $iFilteredTotal, $aaData);
		}
		return $aaData;
	}

	function getChannelHourlyActivity($chan, $type) {
		$sQuery = "SELECT time0,time1,time2,time3,time4,time5,time6,time7,time8,time9,time10,time11,time12,time13,time14,time15,time16,time17,time18,time19,time20,time21,time22,time23
			FROM cstats WHERE chan=:channel AND type=:type";
		$ps = $this->db->prepare($sQuery);
		$ps->bindParam(':type', $type, PDO::PARAM_INT);
		$ps->bindParam(':channel', $chan, PDO::PARAM_STR);
		$ps->execute();
		$result = $ps->fetch(PDO::FETCH_NUM);
		foreach ($result as $key => $val) {
			$result[$key] = (int) $val;
		}
		return $result;
	}

	function getUserGlobalActivity($type, $datatables = false) {
		$aaData = array();

		$sQuery = "SELECT SQL_CALC_FOUND_ROWS uname,letters,words,line AS 'lines',
			actions,smileys,kicks,modes,topics FROM ustats
			WHERE type=:type AND letters>0 and chan='global'";
		if ($datatables) {
			$iTotal = $this->db->datatablesTotal($sQuery, array(':type' => $type));
			$sFiltering = $this->db->datatablesFiltering(array('uname'));
			$sOrdering = $this->db->datatablesOrdering(array('uname', 'letters', 'words', 'line', 'actions', 'smileys', 'kicks', 'modes', 'topics'));
			$sPaging = $this->db->datatablesPaging();
			$sQuery .= sprintf("%s %s %s", $sFiltering ? " AND " . $sFiltering : "", $sOrdering, $sPaging);
		}
		$ps = $this->db->prepare($sQuery);
		$ps->bindParam(':type', $type, PDO::PARAM_INT);
		$ps->execute();
		$data = $ps->fetchAll(PDO::FETCH_ASSOC);
		if ($datatables) {
			$iFilteredTotal = $this->db->foundRows();
		}
		foreach ($data as $row) {
			if ($datatables) {
				$row["DT_RowId"] = $row['uname'];
			}
			$user = $this->getUser('stats', $row['uname']);
			if (!$user) {
				$user = new User();
				$user->nickname = $row['uname'];
				$user->country = 'Unknown';
				$user->country_code = '';
				$user->online = false;
				$user->away = false;
				$user->bot = false;
				$user->service = false;
				$user->operator = false;
				$user->helper = false;
			}
			foreach ($row as $key => $val) {
				$user->$key = $val;
			}
			$aaData[] = $user;
		}
		return $datatables ? $this->db->datatablesOutput($iTotal, $iFilteredTotal, $aaData) : $aaData;
	}

	function getUserHourlyActivity($mode, $user, $chan, $type) {
		$info = $this->getUserData($mode, $user);
		$sQuery = "SELECT time0,time1,time2,time3,time4,time5,time6,time7,time8,time9,time10,time11,time12,time13,time14,time15,time16,time17,time18,time19,time20,time21,time22,time23
			FROM ustats WHERE uname=:uname AND chan=:channel AND type=:type";
		$ps = $this->db->prepare($sQuery);
		$ps->bindParam(':type', $type, PDO::PARAM_INT);
		$ps->bindParam(':channel', $chan, PDO::PARAM_STR);
		$ps->bindParam(':uname', $info['uname'], PDO::PARAM_STR);
		$ps->execute();
		$result = $ps->fetch(PDO::FETCH_NUM);
		foreach ($result as $key => $val) {
			$result[$key] = (int) $val;
		}
		return $result;
	}

	function checkUser($user, $mode) {
		if ($mode == "stats") {
			$query = "SELECT uname FROM ustats WHERE LOWER(uname) = LOWER(:user)";
		} else {
			$query = "SELECT nick FROM user WHERE LOWER(nick) = LOWER(:user)";
		}
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':user', $user, SQL_STR);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_COLUMN) ? true : false;
	}

	/**
	 * Returns the stats username and all aliases of a user
	 * @param string $mode ('stats': $user is a stats user, 'nick': $user is a nickname)
	 * @param string $user Nickname or Stats username
	 * @return array ('nick' => nickname, 'uname' => stats username, 'aliases' => array of aliases)
	 */
	private function getUserData($mode, $user) {
		$uname = ($mode == "stats") ? $user : $this->getUnameFromNick($user);
		$aliases = $this->getUnameAliases($uname);
		if (!$aliases) {
			$aliases = array($uname ? $uname : $user);
		}
		$nick = ($mode == "stats") ? $aliases[0] : $user;
		array_shift($aliases);
		return array('nick' => $nick, 'uname' => $uname, 'aliases' => $aliases);
	}

	/**
	 * Get a user based on its nickname or stats user
	 * @param string $mode 'nick': nickname, 'stats': chanstats user
	 * @param string $user
	 * @return User 
	 */
	function getUser($mode, $user) {
		$info = $this->getUserData($mode, $user);
		$query = sprintf("SELECT u.nick AS nickname, u.realname, u.hostname, u.hiddenhostname AS hostname_cloaked, u.swhois,
			u.username, u.connecttime AS connect_time, u.server, u.away, u.awaymsg AS away_msg, u.ctcpversion AS client, u.online,
			u.lastquit AS quit_time, u.lastquitmsg AS quit_msg, u.countrycode AS country_code, u.country, s.uline AS service
			FROM user u LEFT JOIN server s ON s.servid = u.servid WHERE u.nick = :nickname",
				implode(',', array_map(array('Denora', 'getSqlMode'), str_split(Protocol::user_modes))));
		$ps = $this->db->prepare($query);
		$ps->bindParam(':nickname', $info['nick'], PDO::PARAM_INT);
		$ps->execute();
		$user = $ps->fetchObject('User');
		if ($user) {
			$user->uname = $info['uname'];
			$user->aliases = $info['aliases'];
			return $user;
		} else {
			return null;
		}
	}

	/**
	 * Get a list of channels monitored for a specific user
	 * @param string $mode 'nick': nickname, 'stats': chanstats user
	 * @param string $user
	 * @return array of channel names
	 */
	function getUserChannels($mode, $user) {
		$info = $this->getUserData($mode, $user);
		$secret_mode = Protocol::chan_secret_mode;
		$private_mode = Protocol::chan_private_mode;

		$sWhere = "";
		if ($secret_mode) {
			$sWhere .= sprintf(" AND chan.%s='N'", Denora::getSqlMode($secret_mode));
		}
		if ($private_mode) {
			$sWhere .= sprintf(" AND chan.%s='N'", Denora::getSqlMode($private_mode));
		}
		$hide_channels = $this->cfg->getParam('hide_chans');
		if ($hide_channels) {
			$hide_channels = explode(",", $hide_channels);
			foreach ($hide_channels as $key => $channel) {
				$hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
			}
			$sWhere .= sprintf(" AND LOWER(channel) NOT IN(%s)", implode(',', $hide_channels));
		}

		$query = sprintf("SELECT DISTINCT chan FROM ustats, chan, user WHERE ustats.uname=:uname
			AND ustats.type=0 AND BINARY LOWER(ustats.chan)=LOWER(chan.channel)
			AND user.nick=:nick %s", $sWhere);
		$ps = $this->db->prepare($query);
		$ps->bindParam(':uname', $info['uname'], PDO::PARAM_STR);
		$ps->bindParam(':nick', $info['nick'], PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetchAll(PDO::FETCH_COLUMN);
	}

	function getUserActivity($mode, $user, $chan) {
		$info = $this->getUserData($mode, $user);
		if ($chan == 'global') {
			$sQuery = "SELECT type,letters,words,line AS 'lines',actions,smileys,kicks,modes,topics
				FROM ustats WHERE uname=:uname AND chan=:chan ORDER BY ustats.letters DESC";
		} else {
			$sWhere = "";
			$hide_channels = $this->cfg->getParam('hide_chans');
			if ($hide_channels) {
				$hide_channels = explode(",", $hide_channels);
				foreach ($hide_channels as $key => $channel) {
					$hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
				}
				$sWhere .= sprintf(" AND LOWER(channel) NOT IN(%s)", implode(',', $hide_channels));
			}
			$sQuery = sprintf("SELECT type,letters,words,line AS 'lines',actions,smileys,kicks,modes,topics
				FROM ustats, chan WHERE ustats.uname=:uname AND ustats.chan=:chan
				AND BINARY LOWER(ustats.chan)=LOWER(chan.channel) %s ORDER BY ustats.letters DESC", $sWhere);
		}
		$ps = $this->db->prepare($sQuery);
		$ps->bindParam(':uname', $info['uname'], PDO::PARAM_STR);
		$ps->bindParam(':chan', $chan, PDO::PARAM_STR);
		$ps->execute();
		$data = $ps->fetchAll(PDO::FETCH_ASSOC);
		foreach ($data as $key => $type) {
			foreach ($type as $field => $val) {
				$data[$key][$field] = (int) $val;
			}
		}
		return $data;
	}

	/**
	 * Get the chanstats username assigned to a nick, if available
	 * @param string $nick nickname
	 * @return string chanstats username
	 */
	private function getUnameFromNick($nick) {
		$ps = $this->db->prepare("SELECT uname FROM aliases WHERE nick = :nickname");
		$ps->bindParam(':nickname', $nick, PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetch(PDO::FETCH_COLUMN);
	}

	/**
	 * Get all nicknames linked to a chanstats user
	 * @param string $uname chanstats username
	 * @return array of nicknames
	 */
	private function getUnameAliases($uname) {
		$ps = $this->db->prepare("SELECT a.nick FROM aliases a LEFT JOIN user u ON a.nick = u.nick
			WHERE a.uname = :uname ORDER BY CASE WHEN u.online IS NULL THEN 1 ELSE 0 END,
			u.online DESC, u.lastquit DESC, u.connecttime ASC");
		$ps->bindParam(':uname', $uname, PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetchAll(PDO::FETCH_COLUMN);
	}

}

?>
