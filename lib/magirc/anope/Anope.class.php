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
	
	function __construct() {
		// Get the ircd
		$ircd_file = PATH_ROOT . "lib/magirc/denora/protocol/" . IRCD . ".inc.php";
		if (file_exists($ircd_file)) {
			require_once($ircd_file);
		} else {
			die('<strong>MagIRC</strong> is not properly configured<br />Please configure the ircd in the <a href="admin/">Admin Panel</a>');
		}
		$this->db = Anope_DB::getInstance();
		$this->cfg = new Config();
		require_once(__DIR__ . '/../denora/Objects.class.php');
	}
	
	/**
	 * Gets a list of servers
	 * @return array of Server
	 */
	function getServerList() {
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
	
	/**
	 * Gets the list of current channels
	 * @param boolean $datatables Set true to enable server-side datatables functionality
	 * @return array of Channel
	 */
	function getChannelList($datatables = false) {
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
	 * Gets the global channel activity
	 * @param int $type 0: total, 1: day, 2: week, 3: month, 4: year
	 * @param boolean $datatables true: datatables format, false: standard format
	 * @return array Data
	 * @todo refactor
	 */
	function getChannelGlobalActivity($type, $datatables = false) {
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
				$type = 'monthly';
				break;
			case 2:
				$type = 'weekly';
				break;
			case 3:
				$type = 'daily';
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
}
