<?php
// $Id$

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
			die ('<strong>MagIRC</strong> is not properly configured<br />Please configure the Denora database in the <a href="admin/">Admin Panel</a>');
		}
		$dsn = "mysql:dbname={$db['database']};host={$db['hostname']}";
		$this->connect($dsn, $db['username'], $db['password']) || die('Error opening Denora database<br />'.$this->error);
	}
}

class Denora {

	public $db;
	public $ircd;

	function __construct() {
		$this->db = new Denora_DB();
		require_once(PATH_ROOT."lib/magirc/denora/protocol/".IRCD.".inc.php");
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
						$version = explode("(",substr($result['version'], 0, -1));
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

	// return an array of all servers
	function getServers() {
		return $this->db->selectAll('server', NULL, 'server', 'ASC');
	}

	// return the mode formatted for sql
	function getSqlMode($mode) {
		if (!$mode) {
			return null;
		} elseif (strtoupper($mode) === $mode) {
			return "mode_u".strtolower($mode);
		} else {
			return "mode_l".strtolower($mode);
		}
	}
	
	// CTCP statistics
	function getClientStats($chan = "global") {
		$sql_mode = $this->getSqlMode($this->ircd->getParam("services_protection_mode"));
		$query = "SELECT COUNT(nickid) FROM user WHERE online='Y'";
		if ($sql_mode) {
			$query .= " AND {$sql_mode}='N'";
		}
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$sum = $stmt->fetch(PDO::FETCH_COLUMN);
		
		if ($sql_mode) {
			$query = "SELECT ctcpversion AS name, COUNT(*) AS count FROM user WHERE online='Y' AND {$sql_mode}='N' GROUP by ctcpversion ORDER BY count DESC";
		} else {
			$query = "SELECT ctcpversion AS name, COUNT(*) AS count FROM user WHERE online='Y' GROUP by ctcpversion ORDER BY count DESC";
		}
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return $this->makeData($result, $sum);
	}
	
	function getCountryStats($chan = "global") {
		$query = "SELECT SUM(count) FROM tld WHERE count != 0;";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$sum = $stmt->fetch(PDO::FETCH_COLUMN);
		
		$query = "SELECT country AS name, count FROM tld WHERE count != 0 ORDER BY count DESC;";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
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
				$data[] = array(strtotime("{$date} {$i}:00:00") * 1000, (int) $val["time_".$i]);
			}
		}
		return $data;
	}
	
	function getServerList() {
		$query = "SELECT server, online, comment, currentusers, opers FROM server";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	// return an array of all channels
	function getChannelList($datatables = false) {
		$data = array();
		$i = 0;
		if ($datatables) {
			$chans = $this->db->jsonList('chan', array('channel', 'currentusers', 'maxusers', 'topic'), 'channel');
			echo json_encode($chans); exit;
		}
		$chans = $this->db->selectAll('chan', NULL, 'channel', 'ASC');
		foreach ($chans as $chan) {
			$data[$i]['id'] = $chan['chanid'];
			$data[$i]['name'] = $chan['channel'];
			$data[$i]['users'] = $chan['currentusers'];
			$data[$i]['users_max'] = $chan['maxusers'];
			$data[$i]['users_max_time'] = $chan['maxusertime'];
			$data[$i]['topic'] = $chan['topic'];
			$data[$i]['topic_author'] = $chan['topicauthor'];
			$data[$i]['topic_time'] = strtotime($chan['topictime']);
			$data[$i]['kicks'] = $chan['kickcount'];
			$data[$i]['modes'] = $this->getModes($chan);
			$i++;
		}
		return $data;
	}
	
	private function getModes($chan) {
		$modes = "";
		$j = 97;
		while ($j <= 122) {
			if (@$chan['mode_l'.chr($j)] == "Y") {
				$modes .= chr($j);
			}
			if (@$chan['mode_u'.chr($j)] == "Y") {
				$modes .= chr($j-32);
			}
			$j++;
		}
		if (@$chan['mode_lf_data'] != NULL) {
			$modes .= " ".$chan['mode_lf_data'];
		}
		if (@$chan['mode_lj_data'] != NULL) {
			$modes .= " ".$chan['mode_lj_data'];
		}
		if (@$chan['mode_ll_data'] > 0) {
			$modes .= " ".$chan['mode_ll_data'];
		}
		if (@$chan['mode_uf_data'] != NULL) {
			$modes .= " ".$chan['mode_uf_data'];
		}
		if (@$chan['mode_uj_data'] > 0) {
			$modes .= " ".$chan['mode_uj_data'];
		}
		if (@$chan['mode_ul_data'] != NULL) {
			$modes .= " ".$chan['mode_ul_data'];
		}
		return $modes;
	}

}

?>
