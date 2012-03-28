<?php

class Denora_DB extends DB {

	function __construct() {
		parent::__construct();
		$error = false;
		if (file_exists('conf/denora.cfg.php')) {
			include('conf/denora.cfg.php');
		} elseif (file_exists('../conf/denora.cfg.php')) {
			include('../conf/denora.cfg.php');
		} else {
			$error = true;
		}
		if (!isset($db)) {
			$error = true;
		}
		if ($error) {
			die('<strong>MagIRC</strong> is not properly configured<br />Please configure the Denora database in the <a href="admin/">Admin Panel</a>');
		}
		$dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
		$this->connect($dsn, $db['username'], $db['password']) || die('Error opening Denora database<br />' . $this->error);
	}

}

class Denora {

	private $db;
	private $ircd;
	private $cfg;

	function __construct() {
		$this->db = new Denora_DB();
		$this->cfg = new Config();
		require_once(PATH_ROOT . "lib/magirc/denora/protocol/" . IRCD . ".inc.php");
		$this->ircd = new Protocol();
	}

	// login function
	function login($username, $password) {
		if (!isset($username) || !isset($password)) {
			return false;
		} else {
			return $this->db->selectOne('admin', array('uname' => $username, 'passwd' => md5(trim($password)), 'level' => 1));
		}
	}

	// Returns the Denora version
	function getVersion($what) {
		global $config;
		$table = isset($config['table_server']) ? $config['table_server'] : 'server';
		$query = sprintf("SELECT `version` FROM `%s` WHERE `version` LIKE 'Denora%%'", $table);
		$this->db->query($query, SQL_INIT, SQL_ASSOC);
		$result = $this->db->record;
		if (isset($result['version'])) {
			switch ($what) {
				case 'full':
					$version = explode("-", $result['version']);
					return @$version[1];
					break;
				case 'num':
					$pattern = '/([0-9.]+)/';
					preg_match($pattern, $result['version'], $version);
					return @$version[1];
					break;
				case 'rev':
					$pattern = '/([0-9.]+)/';
					preg_match($pattern, $result['version'], $num);
					if (!$num) {
						$version = explode("(", substr($result['version'], 0, -1));
					} else {
						$pattern = '/(\.[0-9]+)\s/';
						preg_match($pattern, $result['version'], $version);
						$version[1] = substr($version[1], 1);
					}
					return @$version[1];
					break;
				default:
					return NULL;
			}
		} else {
			return NULL;
		}
	}

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

	// return an array of all servers
	/* function getServers() {
	  return $this->db->selectAll('server', NULL, 'server', 'ASC');
	  } */

	// return the mode formatted for sql
	private function getSqlMode($mode) {
		if (!$mode) {
			return null;
		} elseif (strtoupper($mode) === $mode) {
			return "mode_u" . strtolower($mode);
		} else {
			return "mode_l" . strtolower($mode);
		}
	}

	// CTCP statistics
	function getClientStats($chan = "global") {
		if ($chan == "global") {
			$sql_mode = $this->getSqlMode($this->ircd->getParam("services_protection_mode"));
			$query = "SELECT COUNT(nickid) FROM user WHERE online='Y'";
			if ($sql_mode) {
				$query .= " AND {$sql_mode}='N'";
			}
			$stmt = $this->db->prepare($query);
			$stmt->execute();
			$sum = $stmt->fetch(PDO::FETCH_COLUMN);

			if ($sql_mode) {
				$query = "SELECT ctcpversion AS name, COUNT(*) AS count FROM user WHERE online='Y'
				AND {$sql_mode}='N' GROUP by ctcpversion ORDER BY count DESC";
			} else {
				$query = "SELECT ctcpversion AS name, COUNT(*) AS count FROM user WHERE online='Y'
					GROUP by ctcpversion ORDER BY count DESC";
			}
			$stmt = $this->db->prepare($query);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$query = "SELECT COUNT(user.nickid) FROM user, chan, ison WHERE chan.chanid = ison.chanid
				AND user.nickid = ison.nickid AND user.online='Y' AND LOWER(channel)=LOWER(:chan)";
			$stmt = $this->db->prepare($query);
			$stmt->bindParam(':chan', $chan, PDO::PARAM_STR);
			$stmt->execute();
			$sum = $stmt->fetch(PDO::FETCH_COLUMN);

			$query = "SELECT user.ctcpversion AS name, COUNT(*) AS count FROM user, chan, ison WHERE
				user.nickid=ison.nickid AND ison.chanid=chan.chanid AND LOWER(chan.channel)=LOWER(:chan)
				AND user.online='Y' GROUP by user.ctcpversion ORDER BY count DESC;";
			$stmt = $this->db->prepare($query);
			$stmt->bindParam(':chan', $chan, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		return $this->makeData($result, $sum);
	}

	function getCountryStats($chan = "global") {
		if ($chan == "global") {
			$query = "SELECT SUM(count) FROM tld WHERE count != 0;";
			$stmt = $this->db->prepare($query);
			$stmt->execute();
			$sum = $stmt->fetch(PDO::FETCH_COLUMN);

			$query = "SELECT country AS name, count FROM tld WHERE count != 0 ORDER BY count DESC;";
			$stmt = $this->db->prepare($query);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$query = ("SELECT COUNT(user.nickid) FROM chan, ison, user WHERE chan.chanid = ison.chanid
				AND ison.nickid = user.nickid AND LOWER(channel)=LOWER(:chan) AND user.online='Y'");
			$stmt = $this->db->prepare($query);
			$stmt->bindParam(':chan', $chan, PDO::PARAM_STR);
			$stmt->execute();
			$sum = $stmt->fetch(PDO::FETCH_COLUMN);

			$query = "SELECT user.country AS name, COUNT(*) AS count FROM user, chan, ison WHERE
				user.nickid=ison.nickid AND ison.chanid=chan.chanid AND LOWER(chan.channel)=LOWER(:chan)
				AND user.online='Y' GROUP by user.country ORDER BY count DESC";
			$stmt = $this->db->prepare($query);
			$stmt->bindParam(':chan', $chan, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		return $this->makeData($result, $sum);
	}

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

	function getHourlyStats($table) {
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

	function getServerList() {
		$sWhere = "";
		$hide_servers = $this->cfg->getParam('hide_servers');
		if ($hide_servers) {
			$hide_servers = explode(",", $hide_servers);
			foreach ($hide_servers as $key => $server) {
				$hide_servers[$key] = $this->db->escape(trim($server));
			}
			$sWhere .= sprintf("%s LOWER(server) NOT IN(%s)", $sWhere ? " AND " : "WHERE ", implode(',', $hide_servers));
		}
		if ($this->cfg->getParam('hide_ulined')) {
			$sWhere .= $sWhere ? " AND uline = 0" : "WHERE uline = 0";
		}
		$query = "SELECT server, online, comment, currentusers, opers FROM server $sWhere";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function getServer($server) {
		$query = "SELECT server, online, comment, connecttime, lastsplit, version,
			uptime, motd, currentusers, maxusers, FROM_UNIXTIME(maxusertime) AS maxusertime, ping, highestping,
			FROM_UNIXTIME(maxpingtime) AS maxpingtime, opers, maxopers, FROM_UNIXTIME(maxopertime) AS maxopertime
			FROM server WHERE server = :server";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':server', $server, PDO::PARAM_STR);
		$stmt->execute();
		$server = $stmt->fetch(PDO::FETCH_ASSOC);
		$server['motd_html'] = $this->irc2html($server['motd']);
		return $server;
	}

	function getOperatorList() {
		$array = array();
		$ho = $this->ircd->getParam('oper_hidden_mode') ? "AND user." . $this->getSqlMode($this->ircd->getParam('oper_hidden_mode')) . " = 'N'" : NULL;
		$hu = $this->cfg->getParam('hide_ulined') ? "AND server.uline = '0'" : NULL;
		$hs = $this->ircd->getParam('services_protection_mode') ? "AND user." . $this->getSqlMode($this->ircd->getParam('services_protection_mode')) . " = 'N'" : NULL;
		if (IRCD == "unreal32") {
			$query = "SELECT user.*,server.uline FROM user,server WHERE (user.mode_un = 'Y' OR user.mode_ua = 'Y' OR user.mode_la = 'Y' OR user.mode_uc = 'Y' OR user.mode_lo = 'Y')
				AND user.online = 'Y' $ho $hs AND user.server = server.server $hu ORDER BY user.mode_un,user.mode_ua,user.mode_la,user.mode_uc,user.mode_lo,user.nick ASC";
		} else {
			$query = "SELECT user.*,server.uline FROM user,server WHERE user.mode_lo = 'Y' AND user.online = 'Y' $ho $hs AND user.server = server.server $hu ORDER BY user.nick ASC";
		}
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$data = array();
			$data['nick'] = $row['nick'];
			$data['server'] = $row['server'];
			$data['connecttime'] = $row['connecttime'] ? $row['connecttime'] : NULL;
			$data['away'] = $row['away'] == "Y" ? true : false;
			if (IRCD == "unreal32") {
				if ($row['mode_un'] == "Y")
					$level = "Network Admin";
				elseif ($row['mode_ua'] == "Y")
					$level = "Server Admin";
				elseif ($row['mode_la'] == "Y")
					$level = "Services Admin";
				elseif ($row['mode_uc'] == "Y")
					$level = "Co-Admin";
				elseif ($row['mode_lo'] == "Y")
					$level = "Global Operator";
			} else {
				$level = "Operator";
			}
			$data['level'] = $level;
			$data['bot'] = $this->ircd->getParam('bot_mode') && $row[$this->getSqlMode($this->ircd->getParam('bot_mode'))] == 'Y';
			$data['helper'] = $this->ircd->getParam('helper_mode') && $row[$this->getSqlMode($this->ircd->getParam('helper_mode'))] == 'Y';
			$data['uline'] = $row['uline'] ? true : false;
			$array[] = $data;
		}
		return $array;
	}

	// return an array of all channels
	function getChannelList($datatables = false) {
		$aaData = array();
		$secret_mode = $this->ircd->getParam('chan_secret_mode');
		$private_mode = $this->ircd->getParam('chan_private_mode');

		$sWhere = "currentusers > 0";
		if ($secret_mode) {
			$sWhere .= sprintf(" AND %s='N'", $this->getSqlMode($secret_mode));
		}
		if ($private_mode) {
			$sWhere .= sprintf(" AND %s='N'", $this->getSqlMode($private_mode));
		}
		$hide_channels = $this->cfg->getParam('hide_chans');
		if ($hide_channels) {
			$hide_channels = explode(",", $hide_channels);
			foreach ($hide_channels as $key => $channel) {
				$hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
			}
			$sWhere .= sprintf("%s LOWER(channel) NOT IN(%s)", $sWhere ? " AND " : "WHERE ", implode(',', $hide_channels));
		}

		$sQuery = sprintf("SELECT SQL_CALC_FOUND_ROWS * FROM chan WHERE %s", $sWhere);
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
		foreach ($ps->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$aData = array(
				'id' => $row['chanid'],
				'name' => $row['channel'],
				'users' => $row['currentusers'],
				'users_max' => $row['maxusers'],
				'users_max_time' => $row['maxusertime'],
				'topic' => $row['topic'],
				'topic_html' => $this->irc2html($row['topic']),
				'topic_author' => $row['topicauthor'],
				'topic_time' => strtotime($row['topictime']),
				'kicks' => $row['kickcount'],
				'modes' => $this->getChannelModes($row)
			);
			if ($datatables) {
				$aData["DT_RowId"] = $row['channel'];
			}
			$aaData[] = $aData;
		}
		if ($datatables) {
			$iFilteredTotal = $this->db->foundRows();
			return $this->db->datatablesOutput($iTotal, $iFilteredTotal, $aaData);
		}
		return $aaData;
	}

	function getChannelBiggest($limit = 10) {
		$secret_mode = $this->ircd->getParam('chan_secret_mode');
		$private_mode = $this->ircd->getParam('chan_private_mode');
		$query = "SELECT * FROM chan WHERE currentusers > 0";
		if ($secret_mode) {
			$query .= sprintf(" AND %s='N'", $this->getSqlMode($secret_mode));
		}
		if ($private_mode) {
			$query .= sprintf(" AND %s='N'", $this->getSqlMode($private_mode));
		}
		$hide_chans = explode(",", $this->cfg->getParam('hide_chans'));
		for ($i = 0; $i < count($hide_chans); $i++) {
			$query .= " AND LOWER(channel) NOT LIKE " . $this->db->escape(strtolower($hide_chans[$i]));
		}
		$query .= " ORDER BY currentusers DESC LIMIT :limit";
		$ps = $this->db->prepare($query);
		$ps->bindParam(':limit', $limit, PDO::PARAM_INT);
		$ps->execute();
		return $ps->fetchAll(PDO::FETCH_ASSOC);
	}

	function getChannelTop($limit = 10) {
		$secret_mode = $this->ircd->getParam('chan_secret_mode');
		$private_mode = $this->ircd->getParam('chan_private_mode');
		$query = "SELECT chan, line FROM cstats, chan WHERE BINARY LOWER(cstats.chan)=LOWER(chan.channel) AND cstats.type=1 AND cstats.line >= 1";
		if ($secret_mode) {
			$query .= sprintf(" AND chan.%s='N'", $this->getSqlMode($secret_mode));
		}
		if ($private_mode) {
			$query .= sprintf(" AND chan.%s='N'", $this->getSqlMode($private_mode));
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

	function getUsersTop($limit = 10) {
		$ps = $this->db->prepare("SELECT uname, line FROM ustats WHERE type = 1 AND chan='global' AND line >= 1 ORDER BY line DESC LIMIT :limit");
		$ps->bindParam(':limit', $limit, PDO::PARAM_INT);
		$ps->execute();
		return $ps->fetchAll(PDO::FETCH_ASSOC);
	}

	function getChannel($chan) {
		$chan = $this->db->selectOne('chan', array('channel' => $chan));
		if ($chan) {
			return array(
				'id' => $chan['chanid'],
				'name' => $chan['channel'],
				'users' => $chan['currentusers'],
				'users_max' => $chan['maxusers'],
				'users_max_time' => date('Y-m-d H:i:s', $chan['maxusertime']),
				'topic' => $chan['topic'],
				'topic_html' => $this->irc2html($chan['topic']),
				'topic_author' => $chan['topicauthor'],
				'topic_time' => $chan['topictime'],
				'kicks' => $chan['kickcount'],
				'modes' => $this->getChannelModes($chan)
			);
		}
		return null;
	}

	/* Checks if given channel can be displayed
	 * 0 = not existing, 1 = denied, 2 = ok */

	function checkChannel($chan) {
		$noshow = array();
		$no = explode(",", $this->cfg->getParam('hide_chans'));
		for ($i = 0; $i < count($no); $i++) {
			$noshow[$i] = strtolower($no[$i]);
		}
		if (in_array(strtolower($chan), $noshow))
			return 1;

		$stmt = $this->db->prepare("SELECT * FROM `chan` WHERE BINARY LOWER(`channel`) = LOWER(:channel)");
		$stmt->bindParam(':channel', $chan, SQL_STR);
		$stmt->execute();
		$data = $stmt->fetch();

		if (!$data) {
			return 0;
		} else {
			if (@$data['mode_li'] == "Y" || @$data['mode_lk'] == "Y" || @$data['mode_uo'] == "Y") {
				return 1;
			} else {
				return 2;
			}
		}
	}

	function getChannelUsers($chan) {
		if ($this->checkChannel($chan) < 2) {
			return null;
		}
		$array = array();
		$i = 0;
		$query = "SELECT ";
		if ($this->ircd->getParam('helper_mode')) {
			$query .= sprintf("`user`.`%s` AS 'helper', ", $this->getSqlMode($this->ircd->getParam('helper_mode')));
		}
		$query .= "`user`.*, `ison`.*,`server`.`uline`
		FROM `ison`,`chan`,`user`,`server`
		WHERE LOWER(`chan`.`channel`) = LOWER(:channel)
			AND `ison`.`chanid` =`chan`.`chanid`
			AND `ison`.`nickid` =`user`.`nickid`
			AND `user`.`server` = `server`.`server`
		ORDER BY `user`.`nick` ASC";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':channel', $chan, SQL_STR);
		$stmt->execute();

		while ($data = $stmt->fetch()) {
			if (isset($data['nick'])) {
				$mode = NULL;
				if (@$data['mode_lq'] == 'Y') {
					$mode .= "q";
				}
				if (@$data['mode_la'] == 'Y') {
					$mode .= "a";
				}
				if ($data['mode_lo'] == 'Y') {
					$mode .= "o";
				}
				if (@$data['mode_lh'] == 'Y') {
					$mode .= "h";
				}
				if ($data['mode_lv'] == 'Y') {
					$mode .= "v";
				}
				$array[$i]['nick'] = $data['nick'];
				$array[$i]['modes'] = ($mode ? "+" . $mode : "");
				$array[$i]['host'] = ((!empty($data['hiddenhostname']) && $data['hiddenhostname'] != "(null)") ? $data['hiddenhostname'] : $data['hostname']);
				$array[$i]['username'] = $data['username'];
				$array[$i]['countrycode'] = $data['countrycode'];
				$array[$i]['country'] = $data['country'];
				$array[$i]['bot'] = $data[$this->getSqlMode($this->ircd->getParam('bot_mode'))] == 'Y' ? true : false;
				$array[$i]['away'] = $data['away'] == 'Y' ? true : false;
				$array[$i]['online'] = $data['online'] == 'Y' ? true : false;
				$array[$i]['uline'] = $data['uline'] == '1' ? true : false;
				$array[$i]['helper'] = $data['helper'] == 'Y' ? true : false;
				$i++;
			}
		}

		return $array;
	}
	
	function getChannelGlobalActivity($type, $datatables = false) {
		$aaData = array();
		$secret_mode = $this->ircd->getParam('chan_secret_mode');
		$private_mode = $this->ircd->getParam('chan_private_mode');
		
		$sWhere = "cstats.letters>0";
		if ($secret_mode) {
			$sWhere .= sprintf(" AND chan.%s='N'", $this->getSqlMode($secret_mode));
		}
		if ($private_mode) {
			$sWhere .= sprintf(" AND chan.%s='N'", $this->getSqlMode($private_mode));
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
		$sQuery = "SELECT SQL_CALC_FOUND_ROWS uname AS name,letters,words,line AS 'lines',actions,smileys,kicks,modes,topics FROM ustats WHERE chan=:channel AND type=:type AND letters > 0 ";
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

	private function getChannelModes($chan) {
		$modes = "";
		$j = 97;
		while ($j <= 122) {
			if (@$chan['mode_l' . chr($j)] == "Y") {
				$modes .= chr($j);
			}
			if (@$chan['mode_u' . chr($j)] == "Y") {
				$modes .= chr($j - 32);
			}
			$j++;
		}
		if (@$chan['mode_lf_data'] != NULL) {
			$modes .= " " . $chan['mode_lf_data'];
		}
		if (@$chan['mode_lj_data'] != NULL) {
			$modes .= " " . $chan['mode_lj_data'];
		}
		if (@$chan['mode_ll_data'] > 0) {
			$modes .= " " . $chan['mode_ll_data'];
		}
		if (@$chan['mode_uf_data'] != NULL) {
			$modes .= " " . $chan['mode_uf_data'];
		}
		if (@$chan['mode_uj_data'] > 0) {
			$modes .= " " . $chan['mode_uj_data'];
		}
		if (@$chan['mode_ul_data'] != NULL) {
			$modes .= " " . $chan['mode_ul_data'];
		}
		return $modes;
	}
	
	function getUserGlobalActivity($type, $datatables = false) {
		$aaData = array();
		
		$sQuery = "SELECT SQL_CALC_FOUND_ROWS uname AS name,letters,words,line AS 'lines',actions,smileys,kicks,modes,topics FROM ustats
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
	
	function getUser($mode, $user) {
		$uname = ($mode == "stats") ? $user : $this->getUnameFromNick($user);
		$aliases = $this->getUnameAliases($uname);
		$nick = ($mode == "stats") ? $aliases[0] : $user;
		array_shift($aliases);
		
		$ps = $this->db->prepare("SELECT realname, hostname, hiddenhostname, username, swhois,
			account, connecttime, server, away, awaymsg, ctcpversion, online, lastquit,
			lastquitmsg, countrycode, country FROM user WHERE nick = :nickname");
		$ps->bindParam(':nickname', $nick, PDO::PARAM_INT);
		$ps->execute();
		$data = $ps->fetch(PDO::FETCH_OBJ);
		
		$user = array(
			'nick' => $nick,
			'uname' => $uname,
			'aliases' => $aliases,
			'username' => $data->username,
			'realname' => $data->realname,
			'hostname' => $this->ircd->getParam('host_cloaking') ? $data->hiddenhostname : $data->hostname,
			'connected_time' => $data->connecttime,
			'lastquit_time' => $data->lastquit,
			'lastquit_msg' => $data->lastquitmsg,
			'server' => $data->server,
			'client' => $data->ctcpversion,
			'country' => $data->country,
			'country_code' => $data->countrycode,
			'online' => $data->online == 'Y',
			'away' => $data->away == 'Y',
			'away_msg' => $data->awaymsg
		);
		return $user;
	}
	
	private function getUnameFromNick($nick) {
		$ps = $this->db->prepare("SELECT uname FROM aliases WHERE nick = :nickname");
		$ps->bindParam(':nickname', $nick, PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetch(PDO::FETCH_COLUMN);
	}
	
	private function getUnameAliases($uname) {
		$ps = $this->db->prepare("SELECT a.nick FROM aliases a LEFT JOIN user u ON a.nick = u.nick
			WHERE a.uname = :uname ORDER BY CASE WHEN u.online IS NULL THEN 1 ELSE 0 END, 
			u.online DESC, u.lastquit DESC, u.connecttime ASC");
		$ps->bindParam(':uname', $uname, PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetchAll(PDO::FETCH_COLUMN);
	}

	private function irc2html($text) {
		global $charset;
		$lines = explode("\n", utf8_decode($text));
		$out = '';

		foreach ($lines as $line) {
			$line = nl2br(htmlentities($line, ENT_COMPAT, $charset));
			// replace control codes
			$line = preg_replace_callback('/[\003](\d{0,2})(,\d{1,2})?([^\003\x0F]*)(?:[\003](?!\d))?/', function($matches) {
						$colors = array('#FFFFFF', '#000000', '#00007F', '#009300', '#FF0000', '#7F0000', '#9C009C', '#FC7F00', '#FFFF00', '#00FC00', '#009393', '#00FFFF', '#0000FC', '#FF00FF', '#7F7F7F', '#D2D2D2');
						$options = '';

						if ($matches[2] != '') {
							$bgcolor = trim(substr($matches[2], 1));
							if ((int) $bgcolor < count($colors)) {
								$options .= 'background-color: ' . $colors[(int) $bgcolor] . '; ';
							}
						}

						$forecolor = trim($matches[1]);
						if ($forecolor != '' && (int) $forecolor < count($colors)) {
							$options .= 'color: ' . $colors[(int) $forecolor] . ';';
						}

						if ($options != '') {
							return '<span style="' . $options . '">' . $matches[3] . '</span>';
						} else {
							return $matches[3];
						}
					}, $line);
			$line = preg_replace('/[\002]([^\002\x0F]*)(?:[\002])?/', '<strong>$1</strong>', $line);
			$line = preg_replace('/[\x1F]([^\x1F\x0F]*)(?:[\x1F])?/', '<span style="text-decoration: underline;">$1</span>', $line);
			$line = preg_replace('/[\x12]([^\x12\x0F]*)(?:[\x12])?/', '<span style="text-decoration: line-through;">$1</span>', $line);
			$line = preg_replace('/[\x16]([^\x16\x0F]*)(?:[\x16])?/', '<span style="font-style: italic;">$1</span>', $line);
			$line = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\S+]*(\?\S+)?)?)?)@', "<a href='$1' class='topic'>$1</a>", $line);
			// remove dirt
			$line = preg_replace('/[\x00-\x1F]/', '', $line);
			$line = preg_replace('/[\x7F-\xFF]/', '', $line);
			// append line
			if ($line != '') {
				$out .= $line;
			}
		}

		return $out;
	}

}

?>
