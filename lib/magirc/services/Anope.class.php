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
		require_once(__DIR__.'/../objects/anope/Server.class.php');
		require_once(__DIR__.'/../objects/anope/Channel.class.php');
		require_once(__DIR__.'/../objects/anope/User.class.php');
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
				JOIN anope_chan AS chan ON chan.chanid = ison.chanid
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
		if ($mode == 'channel' && $target) $stmt->bindValue(':chan', $target, PDO::PARAM_STR);
		if ($mode == 'server' && $target) $stmt->bindValue(':server', $target, PDO::PARAM_STR);
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
				JOIN anope_chan AS chan ON ison.chanid = chan.chanid
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
		if ($mode == 'channel' && $target) $stmt->bindValue(':chan', $target, PDO::PARAM_STR);
		if ($mode == 'server' && $target) $stmt->bindValue(':server', $target, PDO::PARAM_STR);
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
		//TODO: MISSING! opers, country, countrycode AS country_code
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_CLASS, 'Server');
	}
	
	/**
	 * Gets a server
	 * @param string $server Server name
	 * @return Server
	 */
	public function getServer($server) {
		$query = "SELECT s.name AS server, online, comment AS description, link_time AS connect_time, split_time, version, currentusers AS users, maxusers AS users_max, maxtime AS users_max_time
			FROM anope_server AS s
			LEFT JOIN anope_maxusers AS m ON m.name = s.name
			WHERE s.name = :server
";
		//TODO: MISSING! uptime, motd, ping, highestping AS ping_max, FROM_UNIXTIME(maxpingtime) AS ping_max_time, opers, maxopers AS opers_max, FROM_UNIXTIME(maxopertime) AS opers_max_time, country, countrycode AS country_code
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':server', $server, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchObject('Server');
	}
	
	/**
	 * Get the list of Operators currently online
	 * @return array of User
	 */	
	public function getOperatorList() {
		$query = "SELECT u.nick AS nickname, u.realname, u.host AS hostname, u.chost AS hostname_cloaked,
			u.ident AS username, u.signon AS connect_time, u.server, u.away, u.awaymsg AS away_msg, u.version AS client,
			u.geocode AS country_code, u.geocountry AS country, s.ulined AS service, u.modes AS umodes
			FROM anope_user AS u
			LEFT JOIN anope_server AS s ON s.id = u.servid WHERE";
		//TODO: MISSING! u.online, u.swhois, u.lastquit AS quit_time, u.lastquitmsg AS quit_msg, s.country AS server_country, s.countrycode AS server_country_code
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

		$sQuery = sprintf("SELECT SQL_CALC_FOUND_ROWS channel, currentusers AS users, topic, topicauthor AS topic_author, topictime AS topic_time, modes, maxusers AS users_max, maxtime AS users_max_time FROM anope_chan AS c LEFT JOIN anope_maxusers AS m ON m.name = c.channel WHERE %s", $sWhere);
		//TODO: MISSING! kickcount AS kicks
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
		$query = "SELECT channel, currentusers AS users, maxusers AS users_max, maxtime AS users_max_time"
				. " FROM anope_chan AS c"
				. " LEFT JOIN anope_maxusers AS m ON m.name = c.channel"
				. " WHERE currentusers > 0";
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
		$ps->bindValue(':limit', $limit, PDO::PARAM_INT);
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
		$ps->bindValue(':limit', $limit, PDO::PARAM_INT);
		$ps->execute();
		return $ps->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/**
	 * Get the specified channel
	 * @param string $chan Channel
	 * @return Channel
	 */
	public function getChannel($chan) {
		$sQuery = "SELECT channel, currentusers AS users, topic, topicauthor AS topic_author, topictime AS topic_time, modes, maxusers AS users_max, maxtime AS users_max_time
			FROM anope_chan AS c
			LEFT JOIN anope_maxusers AS m ON m.name = c.channel
			WHERE BINARY LOWER(channel) = LOWER(:chan)";
		//TODO: MISSING! kickcount AS kicks
		$ps = $this->db->prepare($sQuery);
		$ps->bindValue(':chan', $chan, PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetchObject('Channel');
	}
	
	/**
	 * Get the users currently in the specified channel
	 * @todo implement server-side datatables support
	 * @param string $chan Channel
	 * @return array of User
	 */
	public function getChannelUsers($chan) {
		if ($this->checkChannel($chan) != 200) {
			return null;
		}
		$query = "SELECT u.nick AS nickname, u.realname, u.host AS hostname, u.chost AS hostname_cloaked,
			u.ident AS username, u.signon AS connect_time, u.server, u.away, u.awaymsg AS away_msg, u.version AS client,
			u.geocode AS country_code, u.geocountry AS country, s.ulined AS service, i.modes AS cmodes
			FROM anope_ison AS i, anope_chan AS c, anope_user AS u, anope_server AS s
			WHERE LOWER(c.channel) = LOWER(:channel)
				AND i.chanid = c.chanid
				AND i.nickid = u.nickid
				AND u.server = s.name
			ORDER BY u.nick ASC";
		//TODO: MISSING! u.swhois, u.online, u.lastquit AS quit_time, u.lastquitmsg AS quit_msg, s.country AS server_country, s.countrycode AS server_country_code
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':channel', $chan, SQL_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
	}
	
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
		$type = self::getChanstatsType($type);
		if ($datatables) {
			$iTotal = $this->db->datatablesTotal($sQuery, array(':type' => $type));
			$sFiltering = $this->db->datatablesFiltering(array('anope_chanstats.chan', 'anope_chan.topic'));
			$sOrdering = $this->db->datatablesOrdering(array('chan', 'letters', 'words', 'line', 'actions', 'smileys', 'kicks', 'modes', 'topics'));
			$sPaging = $this->db->datatablesPaging();
			$sQuery .= sprintf("%s %s %s", $sFiltering ? " AND " . $sFiltering : "", $sOrdering, $sPaging);
		}
		$ps = $this->db->prepare($sQuery);
		$ps->bindValue(':type', $type, PDO::PARAM_INT);
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
	
	/**
	 * Gets the channel activity for the given channel
	 * @param string $chan Channel
	 * @param int $type 0: total, 1: day, 2: week, 3: month, 4: year
	 * @param boolean $datatables true: datatables format, false: standard format
	 * @return User
	 * @todo refactor
	 */
	public function getChannelActivity($chan, $type, $datatables = false) {
		$aaData = array();
		$sQuery = "SELECT SQL_CALC_FOUND_ROWS nick AS uname, letters, words, line AS 'lines', actions,"
				. " (smileys_happy + smileys_sad + smileys_other) AS smileys, kicks, modes, topics"
				. " FROM anope_chanstats WHERE chan = :channel AND nick != '' AND type=:type AND letters > 0 ";
		$type = self::getChanstatsType($type);
		if ($datatables) {
			$iTotal = $this->db->datatablesTotal($sQuery, array(':type' => $type, ':channel' => $chan));
			$sFiltering = $this->db->datatablesFiltering(array('uname'));
			$sOrdering = $this->db->datatablesOrdering(array('uname', 'letters', 'words', 'line', 'actions', 'smileys', 'kicks', 'modes', 'topics'));
			$sPaging = $this->db->datatablesPaging();
			$sQuery .= sprintf("%s %s %s", $sFiltering ? " AND " . $sFiltering : "", $sOrdering, $sPaging);
		}
		$ps = $this->db->prepare($sQuery);
		$ps->bindValue(':type', $type, PDO::PARAM_INT);
		$ps->bindValue(':channel', $chan, PDO::PARAM_STR);
		$ps->execute();
		$data = $ps->fetchAll(PDO::FETCH_ASSOC);
		if ($datatables) {
			$iFilteredTotal = $this->db->foundRows();
		}
		foreach ($data as $row) {
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
			return $this->db->datatablesOutput($iTotal, $iFilteredTotal, $aaData);
		}
		return $aaData;
	}
	
	/**
	 * Get the hourly average activity for the given channel
	 * @param string $chan Channel
	 * @param int $type int $type 0: total, 1: day, 2: week, 3: month, 4: year
	 * @return mixed
	 */
	public function getChannelHourlyActivity($chan, $type) {
		$sQuery = "SELECT time0,time1,time2,time3,time4,time5,time6,time7,time8,time9,time10,time11,time12,time13,time14,time15,time16,time17,time18,time19,time20,time21,time22,time23
			FROM anope_chanstats WHERE chan=:channel AND type=:type";
		$ps = $this->db->prepare($sQuery);
		$ps->bindValue(':type', self::getChanstatsType($type), PDO::PARAM_INT);
		$ps->bindValue(':channel', $chan, PDO::PARAM_STR);
		$ps->execute();
		$result = $ps->fetch(PDO::FETCH_NUM);
		if (is_array($result)) {
			foreach ($result as $key => $val) {
				$result[$key] = (int) $val;
			}
			return $result;
		} else {
			return null;
		}
	}
	
	/**
	 * Checks if given channel can be displayed
	 * @param string $chan
	 * @return int code (200: OK, 404: not existing, 403: denied)
	 */
	public function checkChannel($chan) {
		$noshow = array();
		$no = explode(",", $this->cfg->hide_chans);
		for ($i = 0; $i < count($no); $i++) {
			$noshow[$i] = strtolower($no[$i]);
		}
		if (in_array(strtolower($chan), $noshow))
			return 403;

		$stmt = $this->db->prepare("SELECT * FROM `anope_chan` WHERE BINARY LOWER(`channel`) = LOWER(:channel)");
		$stmt->bindValue(':channel', $chan, SQL_STR);
		$stmt->execute();
		$data = $stmt->fetch();

		if (!$data) {
			return 404;
		} else {
			if ($this->cfg->block_spchans) {
				if (Protocol::chan_secret_mode && strpos($data['modes'], Protocol::chan_secret_mode) !== false) return 403;
				if (Protocol::chan_private_mode && strpos($data['modes'], Protocol::chan_private_mode) !== false) return 403;
			}
			if (strpos($data['modes'], 'i') !== false || strpos($data['modes'], 'k') !== false || strpos($data['modes'], 'O') !== false) {
				return 403;
			} else {
				return 200;
			}
		}
	}

	/**
	 * Checks if the given channel is being monitored by chanstats
	 * @param string $chan Channel
	 * @return boolean true: yes, false: no
	 */
	public function checkChannelStats($chan) {
		$sQuery = "SELECT COUNT(*) FROM anope_chanstats WHERE chan=:channel";
		$ps = $this->db->prepare($sQuery);
		$ps->bindValue(':channel', $chan, PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetch(PDO::FETCH_COLUMN) ? true : false;
	}
	
	/**
	 * Get the most active current users
	 * @param int $limit
	 * @return array of user stats
	 */
	public function getUsersTop($limit = 10) {
		$aaData = array();
		$ps = $this->db->prepare("SELECT nick AS uname, line AS 'lines' FROM anope_chanstats WHERE type = 'daily' AND chan='' AND line > 0 ORDER BY line DESC LIMIT :limit");
		$ps->bindValue(':limit', $limit, PDO::PARAM_INT);
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
	
	/**
	 * Get a user based on its nickname or stats user
	 * @param string $mode 'nick': nickname, 'stats': chanstats user
	 * @param string $user
	 * @return User
	 */
	public function getUser($mode, $user) {
		$info = $this->getUserData($mode, $user);
		$query = "SELECT u.nick AS nickname, u.realname, u.host AS hostname, u.chost AS hostname_cloaked,
			u.ident AS username, u.signon AS connect_time, u.server, u.away, u.awaymsg AS away_msg, u.version AS client,
			u.geocode AS country_code, u.geocountry AS country, s.ulined AS service, u.modes AS umodes
			FROM anope_user AS u
			LEFT JOIN anope_server AS s ON s.id = u.servid
			WHERE u.nick = :nickname";
		//TODO: MISSING! u.swhois, u.online, u.lastquit AS quit_time, u.lastquitmsg AS quit_msg, s.country AS server_country, s.countrycode AS server_country_code
		$ps = $this->db->prepare($query);
		$ps->bindValue(':nickname', $info['nick'], PDO::PARAM_INT);
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
	public function getUserChannels($mode, $user) {
		$info = $this->getUserData($mode, $user);
		$secret_mode = Protocol::chan_secret_mode;
		$private_mode = Protocol::chan_private_mode;

		$sWhere = "";
		if ($secret_mode) {
			$sWhere .= sprintf(" AND chan.modes NOT LIKE '%%%s%%'", $secret_mode);
		}
		if ($private_mode) {
			$sWhere .= sprintf(" AND chan.modes NOT LIKE '%%%s%%'", $private_mode);
		}
		$hide_channels = $this->cfg->hide_chans;
		if ($hide_channels) {
			$hide_channels = explode(",", $hide_channels);
			foreach ($hide_channels as $key => $channel) {
				$hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
			}
			$sWhere .= sprintf(" AND LOWER(chan.channel) NOT IN(%s)", implode(',', $hide_channels));
		}

		$query = sprintf("SELECT DISTINCT chanstats.chan
			FROM anope_chanstats AS chanstats, anope_chan AS chan, anope_user AS user
			WHERE chanstats.nick = :uname
			AND chanstats.type = 'total' AND BINARY LOWER(chanstats.chan) = LOWER(chan.channel)
			AND user.nick = :nick %s", $sWhere);
		$ps = $this->db->prepare($query);
		$ps->bindValue(':uname', $info['uname'], PDO::PARAM_STR);
		$ps->bindValue(':nick', $info['nick'], PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetchAll(PDO::FETCH_COLUMN);
	}
	
	/**
	 * Get the global user activity
	 * @param int $type int $type 0: total, 1: day, 2: week, 3: month, 4: year
	 * @param boolean $datatables true: datatables format, false: standard format
	 * @return array
	 * @todo refactor
	 */
	public function getUserGlobalActivity($type, $datatables = false) {
		$aaData = array();

		$sQuery = "SELECT SQL_CALC_FOUND_ROWS nick AS 'uname', letters, words, line AS 'lines',
			actions, (smileys_happy + smileys_sad + smileys_other) AS 'smileys', kicks, modes, topics FROM anope_chanstats
			WHERE type=:type AND letters > 0 and chan=''";
		$type = self::getChanstatsType($type);
		if ($datatables) {
			$iTotal = $this->db->datatablesTotal($sQuery, array(':type' => $type));
			$sFiltering = $this->db->datatablesFiltering(array('uname'));
			$sOrdering = $this->db->datatablesOrdering(array('uname', 'letters', 'words', 'line', 'actions', 'smileys', 'kicks', 'modes', 'topics'));
			$sPaging = $this->db->datatablesPaging();
			$sQuery .= sprintf("%s %s %s", $sFiltering ? " AND " . $sFiltering : "", $sOrdering, $sPaging);
		}
		$ps = $this->db->prepare($sQuery);
		$ps->bindValue(':type', $type, PDO::PARAM_INT);
		$ps->execute();
		$data = $ps->fetchAll(PDO::FETCH_ASSOC);
		if ($datatables) {
			$iFilteredTotal = $this->db->foundRows();
		}
		if (is_array($data)) {
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
		}
		return $datatables ? $this->db->datatablesOutput($iTotal, $iFilteredTotal, $aaData) : $aaData;
	}
	
	/**
	 * Get the user activity of the given user
	 * @param string $mode stats: user is treated as stats user, nick: user is treated as nickname
	 * @param string $user User
	 * @param string $chan Channel
	 * @return mixed
	 * @todo refactor
	 */
	public function getUserActivity($mode, $user, $chan) {
		$info = $this->getUserData($mode, $user);
		if ($chan == 'global') {
			$chan = ''; //TODO: this is dirty
			$sQuery = "SELECT type, letters, words, line AS 'lines', actions, (smileys_happy + smileys_sad + smileys_other) AS smileys, kicks, chanstats.modes, topics
				FROM anope_chanstats AS chanstats
				WHERE nick = :nick AND chan = :chan
				ORDER BY chanstats.letters DESC";
		} else {
			$sWhere = "";
			$hide_channels = $this->cfg->hide_chans;
			if ($hide_channels) {
				$hide_channels = explode(",", $hide_channels);
				foreach ($hide_channels as $key => $channel) {
					$hide_channels[$key] = $this->db->escape(trim(strtolower($channel)));
				}
				$sWhere .= sprintf(" AND LOWER(channel) NOT IN(%s)", implode(',', $hide_channels));
			}
			$sQuery = sprintf("SELECT type, letters, words, line AS 'lines', actions, (smileys_happy + smileys_sad + smileys_other) AS smileys, kicks, chanstats.modes, topics
				FROM anope_chanstats AS chanstats, anope_chan AS chan
				WHERE chanstats.nick = :nick AND chanstats.chan = :chan AND BINARY LOWER(chanstats.chan) = LOWER(chan.channel) %s
				ORDER BY chanstats.letters DESC", $sWhere);
		}
		$ps = $this->db->prepare($sQuery);
		$ps->bindValue(':nick', $info['uname'], PDO::PARAM_STR);
		$ps->bindValue(':chan', $chan, PDO::PARAM_STR);
		$ps->execute();
		$data = $ps->fetchAll(PDO::FETCH_ASSOC);
		if (is_array($data)) {
			foreach ($data as $key => $type) {
				foreach ($type as $field => $val) {
					$data[$key][$field] = (int) $val;
				}
			}
			return $data;
		} else {
			return null;
		}
	}
	
	/**
	 * Get the average hourly activity for the given user
	 * @param string $mode stats: user is treated as stats user, nick: user is treated as nickname
	 * @param string $user User
	 * @param string $chan Channel
	 * @param int $type int $type 0: total, 1: day, 2: week, 3: month, 4: year
	 * @return mixed
	 * @todo refactor
	 */
	public function getUserHourlyActivity($mode, $user, $chan, $type) {
		$info = $this->getUserData($mode, $user);
		//TODO: this is dirty
		if ($chan == 'global'){
			$chan = '';
		}
		$sQuery = "SELECT time0,time1,time2,time3,time4,time5,time6,time7,time8,time9,time10,time11,time12,time13,time14,time15,time16,time17,time18,time19,time20,time21,time22,time23
			FROM anope_chanstats WHERE nick = :nick AND chan = :channel AND type = :type";
		$ps = $this->db->prepare($sQuery);
		$ps->bindValue(':type', self::getChanstatsType($type), PDO::PARAM_INT);
		$ps->bindValue(':channel', $chan, PDO::PARAM_STR);
		$ps->bindValue(':nick', $info['uname'], PDO::PARAM_STR);
		$ps->execute();
		$result = $ps->fetch(PDO::FETCH_NUM);
		if (is_array($result)) {
			foreach ($result as $key => $val) {
				$result[$key] = (int) $val;
			}
			return $result;
		} else {
			return null;
		}
	}
	
	/**
	 * Checks if the given user exists
	 * @param string $user User
	 * @param string $mode ('stats': $user is a stats user, 'nick': $user is a nickname)
	 * @return boolean true: yes, false: no
	 */
	public function checkUser($user, $mode) {
		if ($mode == "stats") {
			$query = "SELECT nick FROM anope_user WHERE LOWER(account) = LOWER(:user)";
		} else {
			$query = "SELECT nick FROM anope_user WHERE LOWER(nick) = LOWER(:user)";
		}
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':user', $user, SQL_STR);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_COLUMN) ? true : false;
	}

	/**
	 * Checks if the given user is being monitored by chanstats
	 * @param string $user User
	 * @param string $mode ('stats': $user is a stats user, 'nick': $user is a nickname)
	 * @return boolean true: yes, false: no
	 */
	public function checkUserStats($user, $mode) {
		if ($mode != 'stats') {
			$user = $this->getUnameFromNick($user);
		}
		$sQuery = "SELECT COUNT(*) FROM anope_chanstats WHERE nick = :user";
		$ps = $this->db->prepare($sQuery);
		$ps->bindValue(':user', $user, PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetch(PDO::FETCH_COLUMN) ? true : false;
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
	 * Get the chanstats username assigned to a nick, if available
	 * @param string $nick nickname
	 * @return string chanstats username
	 */
	private function getUnameFromNick($nick) {
		$ps = $this->db->prepare("SELECT account FROM anope_user WHERE nick = :nickname");
		$ps->bindValue(':nickname', $nick, PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetch(PDO::FETCH_COLUMN);
	}

	/**
	 * Get all nicknames linked to a chanstats user
	 * @param string $uname chanstats username
	 * @return array of nicknames
	 */
	private function getUnameAliases($uname) {
		$ps = $this->db->prepare("SELECT u.nick
			FROM anope_user AS u
			WHERE u.account = :uname
			ORDER BY u.signon ASC");
		$ps->bindValue(':uname', $uname, PDO::PARAM_STR);
		$ps->execute();
		return $ps->fetchAll(PDO::FETCH_COLUMN);
	}
	
	/**
	 * Maps the denora style chanstats type to the anope names
	 * @param type $type
	 * @return string
	 */
	private static function getChanstatsType($type) {
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

}
