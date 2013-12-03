<?php

// Database configuration
class Anope_DB extends DB {
	private static $instance = NULL;

	public static function getInstance() {
		if (is_null(self::$instance) === true) {
			// Check the database configuration
			$db = null;
			$error = false;
			$config_file = PATH_ROOT . 'conf/anope.cfg.php';
			if (file_exists($config_file)) {
				include($config_file);
			} else {
				$error = true;
			}
			if ($error || !is_array($db)) {
				die('<strong>MagIRC</strong> is not properly configured<br />Please configure the Anope database in the <a href="admin/">Admin Panel</a>');
			}
			$dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
			$args = array();
			if (isset($db['ssl']) && $db['ssl_key']) $args[PDO::MYSQL_ATTR_SSL_KEY] = $db['ssl_key'];
			if (isset($db['ssl']) && $db['ssl_cert']) $args[PDO::MYSQL_ATTR_SSL_CERT] = $db['ssl_cert'];
			if (isset($db['ssl']) && $db['ssl_ca']) $args[PDO::MYSQL_ATTR_SSL_CA] = $db['ssl_ca'];
			self::$instance = new DB($dsn, $db['username'], $db['password'], $args);
			if (self::$instance->error) die('Error opening the Anope database<br />' . self::$instance->error);
		}
		return self::$instance;
	}
}

class Anope implements Service {
	private $db;
	private $cfg;
	
	public function __construct() {
		// Get the ircd
		$ircd_file = PATH_ROOT . "lib/magirc/ircds/" . IRCD . ".inc.php";
		if (file_exists($ircd_file)) {
			require_once($ircd_file);
		} else {
			die('<strong>MagIRC</strong> is not properly configured<br />Please configure the ircd in the <a href="admin/">Admin Panel</a>');
		}
		$this->db = Anope_DB::getInstance();
		$this->cfg = new Config();
	}

	public function getCurrentStatus() {
		$data = array(
			'users' => array('val' => (int) $this->getUserCount(), 'time' => time()),
			'chans' => array('val' => count($this->getChannelList()), 'time' => time()),
			'daily_users' => array('val' => 0, 'time' => 0),
			'servers' => array('val' => count($this->getServerList()), 'time' => time()),
			'opers' => array('val' => count($this->getOperatorList()), 'time' => time())
		);
		return $data;
	}
	
	public function getMaxValues() {
		$data = array(
			'users' => array('val' => 0, 'time' => 0),
			'channels' => array('val' => 0, 'time' => 0),
			'servers' => array('val' => 0, 'time' => 0),
			'opers' => array('val' => 0, 'time' => 0)
		);
		return $data;
	}
	
	public function getUserCount($mode = null, $target = null) {
		$query = "SELECT COUNT(*) FROM anope_user"; //WHERE online = 'Y'
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_COLUMN);
	}
	
	/**
	 * Get CTCP client statistics
	 * @param string $mode Mode ('server', 'channel', null: global)
	 * @param string $target Target
	 * @return array Data
	 */
	public function getClientStats($mode = null, $target = null) {
		$query = "SELECT user.version AS client, COUNT(*) AS count
			FROM anope_user AS user
			JOIN anope_server AS server ON server.id = user.servid";
		if ($mode == 'channel' && $target) {
			$query .= " JOIN anope_ison AS ison ON ison.nickid = user.nickid
				JOIN chan ON chan.chanid = ison.chanid
				WHERE LOWER(chan.channel)=LOWER(:chan)"; // AND user.online='Y'
		} elseif ($mode == 'server' && $target) {
			$query .= " WHERE LOWER(user.server)=LOWER(:server)"; // AND user.online='Y'
		} else {
			//$query .= " WHERE user.online='Y'";
			$query .= " WHERE 0 = 0";
		}
		if ($this->cfg->hide_ulined) {
			$query .= " AND server.ulined = 'N'";
		}
		if (Protocol::services_protection_mode) {
			$query .= sprintf(" AND user.modes NOT LIKE '%%%s%%", Protocol::services_protection_mode);
		}
		$query .= " GROUP by user.version ORDER BY count DESC";
		$stmt = $this->db->prepare($query);
		if ($mode == 'channel' && $target) $stmt->bindParam(':chan', $target, PDO::PARAM_STR);
		if ($mode == 'server' && $target) $stmt->bindParam(':server', $target, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Get GeoIP country statistics
	 * @param string $mode Mode ('server', 'channel', null: global)
	 * @param string $target Target
	 * @return array Data
	 */
	public function getCountryStats($mode = null, $target = null) {		
		$query = "SELECT user.geocountry AS country, user.geocode AS country_code, COUNT(*) AS count
			FROM anope_user AS user
			JOIN anope_server AS server ON server.id = user.servid";
		if ($mode == 'channel' && $target) {
			$query .= " JOIN anope_ison AS ison ON ison.nickid = user.nickid
				JOIN chan ON ison.chanid = chan.chanid
				WHERE LOWER(chan.channel)=LOWER(:chan)"; // AND user.online='Y'
		} elseif ($mode == 'server' && $target) {
			$query .= " WHERE LOWER(user.server)=LOWER(:server)"; // AND user.online='Y'
		} else {
			//$query .= " WHERE user.online='Y'";
			$query .= " WHERE 0 = 0";
		}
		if ($this->cfg->hide_ulined) {
			$query .= " AND server.ulined = 'N'";
		}
		if (Protocol::services_protection_mode) {
			$query .= sprintf(" AND user.modes NOT LIKE '%%%s%%", Protocol::services_protection_mode);
		}
		$query .= " GROUP by user.geocountry ORDER BY count DESC";
		$stmt = $this->db->prepare($query);
		if ($mode == 'channel' && $target) $stmt->bindParam(':chan', $target, PDO::PARAM_STR);
		if ($mode == 'server' && $target) $stmt->bindParam(':server', $target, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/**
	 * Prepare data for use by country pie charts
	 * @param array $result Array of data
	 * @param type $sum user count
	 * @return array of arrays (string 'name', int 'count', double 'y')
	 */
	public function makeCountryPieData($result, $sum) {
		$data = array();
		$unknown = 0;
		$other = 0;
		foreach ($result as $val) {
			$percent = round($val["count"] / $sum * 100, 2);
			if ($percent < 2) {
				$other += $val["count"];
			} elseif (in_array ($val['country'], array(null, "", "Unknown", "localhost"))) {
				$unknown += $val["count"];
			} else {
				$data[] = array('name' => $val['country'], 'count' => $val["count"], 'y' => $percent);
			}
		}
		if ($unknown > 0) {
			$data[] = array('name' => T_gettext('Unknown'), 'count' => $unknown, 'y' => round($unknown / $sum * 100, 2));
		}
		if ($other > 0) {
			$data[] = array('name' => T_gettext('Other'), 'count' => $other, 'y' => round($other / $sum * 100, 2));
		}
		return $data;
	}

	/**
	 * Prepare data for use by client pie charts
	 * @param array $result Array of data
	 * @param type $sum user count
	 * @return array (clients => (name, count, y), versions (name, version, cat, count, y))
	 */
	public function makeClientPieData($result, $sum) {
		$clients = array();
		foreach ($result as $client) {
			// Determine client name and version
			$matches = array();
			preg_match('/^(.*?)\s*(\S*\d\S*)/', str_replace(array('(',')','[',']','{','}'), '', $client['client']), $matches);
			if (count($matches) == 3) {
				$name = $matches[1];
				$version = $matches[2][0] == 'v' ? substr($matches[2], 1) : $matches[2];
			} else {
				$name = $client['client'] ? $client['client'] : T_gettext('Unknown');
				$version = '';
			}
			$name = trim($name);
			$version = trim($version);
			// Categorize the versions
			if (!array_key_exists($name, $clients)) {
				$clients[$name] = array('count' => $client['count'], 'versions' => array());
			} else {
				$clients[$name]['count'] += $client['count'];
			}
			if (isset($clients[$name]['versions'][$version])) {
				$clients[$name]['versions'][$version] += $client['count'];
			} else {
				$clients[$name]['versions'][$version] = $client['count'];
			}
		}
		// Sort by count descending
		uasort($clients, function($a, $b) {
			return $a['count'] < $b['count'];
		});
		foreach ($clients as $key => $val) {
			arsort($clients[$key]['versions']);
			unset($val);
		}

		// Prepare data for output
		$min_count = ceil($sum / 300);
		$data = array('clients' => array(), 'versions' => array());
		$other = array('count' => 0, 'versions' => array());
		$other_various = 0;
		foreach ($clients as $name => $client) {
			$percent = round($client['count'] / $sum * 100, 2);
			if ($percent < 2 || $name == T_gettext('Unknown')) { // Too small or unknown
				$other['count'] += $client['count'];
				foreach ($client['versions'] as $version => $count) {
					if ($count < $min_count) {
						$other_various += $count;
					} else {
						$other['versions'][] = array('name' => $name, 'version' => $version, 'cat' => T_gettext('Other'), 'count' => (int) $count, 'y' => (double) round($count / $sum * 100, 2));
					}
				}
			} else {
				$data_various = 0;
				$data['clients'][] = array('name' => $name, 'count' => (int) $client['count'], 'y' => (double) $percent);
				foreach ($client['versions'] as $version => $count) {
					if ($count < $min_count) {
						$data_various += $count;
					} else {
						$data['versions'][] = array('name' => $name, 'version' => $version, 'cat' => $name, 'count' => (int) $count, 'y' => (double) round($count / $sum * 100, 2));
					}
				}
				if ($data_various) {
					$data['versions'][] = array('name' => $name, 'version' => '('.T_gettext('various').')', 'cat' => $name, 'count' => (int) $data_various, 'y' => (double) round($data_various / $sum * 100, 2));
				}
			}
		}
		if ($other_various) {
			$other['versions'][] = array('name' => T_gettext('Various'), 'version' => '', 'cat' => T_gettext('Other'), 'count' => (int) $other_various, 'y' => (double) round($other_various / $sum * 100, 2));;
		}
		// Append other slices
		if ($other['count'] > 0) {
			$other['percent'] = round($other['count'] / $sum * 100, 2);
			$data['clients'][] = array('name' => T_gettext('Other'), 'count' => (int) $other['count'], 'y' => (double) $other['percent']);
			$data['versions'] = array_merge($data['versions'], $other['versions']);
		}
		#echo "<pre>"; print_r($data); exit;
		return $data;
	}
	
	public function getHourlyStats($table) { }
	
	/**
	 * Gets a list of servers
	 * @return array of Server
	 */
	public function getServerList() {
		$sWhere = "";
		$hide_servers = $this->cfg->hide_servers;
		if ($hide_servers) {
			$hide_servers = explode(",", $hide_servers);
			foreach ($hide_servers as $key => $server) {
				$hide_servers[$key] = $this->db->escape(trim($server));
			}
			$sWhere .= sprintf("WHERE name NOT IN(%s)", implode(",", $hide_servers));
		}
		if ($this->cfg->hide_ulined) {
			$sWhere .= $sWhere ? " AND ulined = 'N'" : "WHERE ulined = 'N'";
		}
		$query = "SELECT name AS server, online, comment AS description, currentusers AS users FROM anope_server $sWhere";
		//opers, country, countrycode AS country_code are missing
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_CLASS, 'Server');
	}
	public function getServer($server) { }
	
	/**
	 * Get the list of Operators currently online
	 * @return array of User
	 */	
	public function getOperatorList() {
		$query = "SELECT u.nick AS nickname, u.realname, u.host AS hostname, u.chost AS hostname_cloaked,
			u.ident AS username, u.signon AS connect_time, u.server, u.away, u.awaymsg AS away_msg, u.version AS client,
			u.geocode AS country_code, u.geocountry AS country, s.ulined AS service, u.modes
			FROM anope_user AS u
			LEFT JOIN anope_server AS s ON s.id = u.servid WHERE";
		//u.online, u.swhois, u.lastquit AS quit_time, u.lastquitmsg AS quit_msg, s.country AS server_country, s.countrycode AS server_country_code missing
		$levels = Protocol::$oper_levels;
		if (!empty($levels)) {
			$i = 1;
			$query .= " (";
			foreach ($levels as $mode => $level) {
				$query .= sprintf("u.modes LIKE '%%%s%'%", $mode);
				if ($i < count($levels)) {
					$query .= " OR ";
				}
				$i++;
			}
			$query .= ")";
		} else {
			$query .= " u.modes LIKE '%o%'";
		}
		//$query .= " AND u.online = 'Y'";
		if (Protocol::oper_hidden_mode) {
			$query .= sprintf(" AND u.modes NOT LIKE '%%%s%%'", Protocol::oper_hidden_mode);
		}
		if (Protocol::services_protection_mode) {
			$query .= sprintf(" AND u.modes NOT LIKE '%%%s%%'", Protocol::services_protection_mode);
		}
		$query .= " AND u.server = s.name";
		if ($this->cfg->hide_ulined) {
			$query .= " AND s.ulined = 'N'";
		}
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
	public function getChannelList($datatables = false) {
		$secret_mode = Protocol::chan_secret_mode;
		$private_mode = Protocol::chan_private_mode;

		$sWhere = "currentusers > 0";
		if ($secret_mode) {
			$sWhere .= sprintf(" AND modes NOT LIKE '%%%s%%'", $secret_mode);
		}
		if ($private_mode) {
			$sWhere .= sprintf(" AND modes NOT LIKE '%%%s%%'", $private_mode);
		}
		$hide_channels = $this->cfg->hide_chans;
		if ($hide_channels) {
			$hide_channels = explode(",", $hide_channels);
			foreach ($hide_channels as $key => $channel) {
				$hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
			}
			$sWhere .= sprintf("%s LOWER(channel) NOT IN(%s)", $sWhere ? " AND " : "WHERE ", implode(",", $hide_channels));
		}

		$sQuery = sprintf("SELECT SQL_CALC_FOUND_ROWS channel, currentusers AS users, topic, topicauthor AS topic_author, topictime AS topic_time, modes FROM anope_chan WHERE %s", $sWhere);
		// maxusers AS users_max, maxusertime AS users_max_time, kickcount AS kicks, are missing
		if ($datatables) {
			$iTotal = $this->db->datatablesTotal($sQuery);
			$sFiltering = $this->db->datatablesFiltering(array('channel', 'topic'));
			$sOrdering = $this->db->datatablesOrdering(array('channel', 'currentusers'));
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
	public function getChannelBiggest($limit = 10) {
		$secret_mode = Protocol::chan_secret_mode;
		$private_mode = Protocol::chan_private_mode;
		$query = "SELECT channel, currentusers AS users FROM anope_chan WHERE currentusers > 0"; //maxusers AS users_max, maxusertime AS users_max_time missing
		if ($secret_mode) {
			$query .= sprintf(" AND modes NOT LIKE '%%%s%%'", $secret_mode);
		}
		if ($private_mode) {
			$query .= sprintf(" AND modes NOT LIKE '%%%s%%'", $private_mode);
		}
		$hide_chans = explode(",", $this->cfg->hide_chans);
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
	public function getChannelTop($limit = 10) {
		$secret_mode = Protocol::chan_secret_mode;
		$private_mode = Protocol::chan_private_mode;
		$query = "SELECT chan AS channel, line AS 'lines' FROM anope_chanstats, anope_chan WHERE BINARY LOWER(anope_chanstats.chan)=LOWER(anope_chan.channel) AND anope_chanstats.type=1 AND anope_chanstats.line >= 1";
		if ($secret_mode) {
			$query .= sprintf(" AND anope_chan.modes NOT LIKE '%%%s%%'", $secret_mode);
		}
		if ($private_mode) {
			$query .= sprintf(" AND anope_chan.modes NOT LIKE '%%%s%%'", $private_mode);
		}
		$hide_chans = explode(",", $this->cfg->hide_chans);
		for ($i = 0; $i < count($hide_chans); $i++) {
			$query .= " AND anope_chanstats.chan NOT LIKE " . $this->db->escape(strtolower($hide_chans[$i]));
		}
		$query .= " ORDER BY anope_chanstats.line DESC LIMIT :limit";
		$ps = $this->db->prepare($query);
		$ps->bindParam(':limit', $limit, PDO::PARAM_INT);
		$ps->execute();
		return $ps->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function getChannel($chan) { }
	public function getChannelUsers($chan) { }
	
	/**
	 * Gets the global channel activity
	 * @param int $type 0: total, 1: day, 2: week, 3: month, 4: year
	 * @param boolean $datatables true: datatables format, false: standard format
	 * @return array Data
	 * @todo refactor
	 */
	public function getChannelGlobalActivity($type, $datatables = false) {
		$aaData = array();
		$secret_mode = Protocol::chan_secret_mode;
		$private_mode = Protocol::chan_private_mode;

		$sWhere = "anope_chanstats.letters > 0";
		if ($secret_mode) {
			$sWhere .= sprintf(" AND anope_chan.modes NOT LIKE '%%%s%%'",$secret_mode);
		}
		if ($private_mode) {
			$sWhere .= sprintf(" AND anope_chan.modes NOT LIKE '%%%s%%'",$private_mode);
		}
		$hide_channels = $this->cfg->hide_chans;
		if ($hide_channels) {
			$hide_channels = explode(",", $hide_channels);
			foreach ($hide_channels as $key => $channel) {
				$hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
			}
			$sWhere .= sprintf(" AND LOWER(anope_chanstats.chan) NOT IN(%s)", implode(',', $hide_channels));
		}

		$sQuery = sprintf("SELECT SQL_CALC_FOUND_ROWS chan AS name,letters,words,line AS 'lines',actions,(smileys_happy + smileys_sad + smileys_other) AS smileys,kicks,anope_chanstats.modes,topics FROM anope_chanstats
			 JOIN anope_chan ON BINARY LOWER(anope_chanstats.chan)=LOWER(anope_chan.channel) WHERE anope_chanstats.type=:type AND %s", $sWhere);
		switch ($type) {
			case 1:
				$type = 'daily';
				break;
			case 2:
				$type = 'weekly';
				break;
			case 3:
				$type = 'monthly';
				break;
			case 0:
			default:
				$type = 'total';
		}
		if ($datatables) {
			$iTotal = $this->db->datatablesTotal($sQuery, array(':type' => $type));
			$sFiltering = $this->db->datatablesFiltering(array('anope_chanstats.chan', 'anope_chan.topic'));
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
	public function getChannelActivity($chan, $type, $datatables = false) { }
	public function getChannelHourlyActivity($chan, $type) { }
	public function checkChannel($chan) { }	
	public function checkChannelStats($chan) { }
	
	/**
	 * Get the most active current users
	 * @param int $limit
	 * @return array of user stats
	 */
	public function getUsersTop($limit = 10) {
		$aaData = array();
		$ps = $this->db->prepare("SELECT nick AS uname, line AS 'lines' FROM anope_chanstats WHERE type = 'daily' AND chan='' AND line > 0 ORDER BY line DESC LIMIT :limit");
		$ps->bindParam(':limit', $limit, PDO::PARAM_INT);
		$ps->execute();
		$data = $ps->fetchAll(PDO::FETCH_ASSOC);
		if (is_array($data)) {
			foreach ($data as $row) {
				$user = $this->getUser('stats', $row['uname']);
				if (!$user) $user = new User();
				$user->uname = $row['uname'];
				$user->lines = $row['lines'];
				$aaData[] = $user;
			}
		}
		return $aaData;
	}
	
	public function getUser($mode, $user) { }
	public function getUserChannels($mode, $user) { }
	public function getUserGlobalActivity($type, $datatables = false) { }
	public function getUserActivity($mode, $user, $chan) { }
	public function getUserHourlyActivity($mode, $user, $chan, $type) { }
	public function checkUser($user, $mode) { }
	public function checkUserStats($user, $mode) { }
	
	public static function getSqlMode($mode) { }
	public static function getSqlModeData($mode) { }

}
