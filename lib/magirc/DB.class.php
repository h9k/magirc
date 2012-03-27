<?php


// define the query types
define('SQL_NONE', 1);
define('SQL_ALL', 2);
define('SQL_INIT', 3);

// define the query formats
define('SQL_ASSOC', PDO::FETCH_ASSOC);
define('SQL_INDEX', PDO::FETCH_NUM);
define('SQL_OBJ', PDO::FETCH_OBJ);

// define the parameter formats
define('SQL_INT', PDO::PARAM_INT);
define('SQL_STR', PDO::PARAM_STR);

class DB {

	private $pdo;
	private $result;
	public $error;
	public $record;

	function __construct() {

	}

	function connect($dsn, $username, $password) {
		try {
			$this->pdo = new PDO($dsn, $username, $password);
			$this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->query("SET NAMES utf8");
			return true;
		} catch(PDOException $e) {
			$this->error = $e->getMessage();
			return false;
		}
	}

	function disconnect() {
		$this->pdo = null;
	}

	function getTables() {
		$query = "SHOW TABLES";
		$this->query($query, SQL_ALL, SQL_INDEX);
		return $this->record;
	}

	function prepare($query) {
		return $this->pdo->prepare($query);
	}

	function query($query, $type = SQL_NONE, $format = SQL_INDEX) {
		try {
			$this->result = $this->pdo->query($query);
			switch ($type) {
				case SQL_ALL:
					$this->record = $this->result->fetchAll($format);
					break;
				case SQL_INIT:
					$this->record = $this->result->fetch($format);
					break;
				case SQL_NONE:
				default:
					break;
			}
			return true;
		} catch(PDOException $e) {
			die("<br />QUERY failure: {$query}<br />".$e->getMessage());
		}
		return true;
	}

	function next($format = SQL_INDEX) {
		$this->record = $this->result->fetch($format);
		if ($this->record) {
			return $this->record;
		} else {
			return false;
		}
	}

	function escape($input, $quotes = true) {
		if ($quotes) {
			return $this->pdo->quote($input);
		} else {
			return substr($this->pdo->quote($input), 1, -1);
		}
	}

	function lastInsertID() {
		return $this->pdo->lastInsertId();
	}

	function numRows() {
		try {
			return $this->result->rowCount();
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}
	function fetchColumn() {
		try {
			return $this->result->fetchColumn();
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}
	function foundRows() {
		$ps = $this->prepare("SELECT FOUND_ROWS()");
		$ps->execute();
		return $ps->fetch(PDO::FETCH_COLUMN);
	}

	private function select($table, $where = NULL, $sort = NULL, $order = 'ASC', $limit = 0, $type = SQL_ALL, $format = SQL_ASSOC) {
		$query = "SELECT * FROM `{$table}`";

		if ($where) {
			$conditions = "";
			foreach($where as $key => $value) {
				$conditions .= sprintf("`%s` = %s AND ", $key, $this->escape($value));
			}
			$query .= " WHERE " . substr($conditions, 0, -5);
		}

		if ($sort) {
			$query .= " ORDER BY `{$sort}` {$order}";
		}

		if ($limit) {
			$query .= " LIMIT {$limit}";
		}

		$this->query($query, $type, $format);
		return $this->record;
	}

	function selectOne($table, $where = NULL, $sort = NULL, $order = 'ASC', $limit = 0) {
		return $this->select($table, $where, $sort, $order, $limit, SQL_INIT, $format = SQL_ASSOC);
	}
	function selectAll($table, $where = NULL, $sort = NULL, $order = 'ASC', $limit = 0) {
		return $this->select($table, $where, $sort, $order, $limit, SQL_ALL, $format = SQL_ASSOC);
	}

	function insert($table, $array) {
		$query = "INSERT INTO `{$table}` SET ";

		foreach($array as $key => $value) {
			$query .= sprintf("`%s` = %s, ", $key, $this->escape($value));
		}
		$query = substr($query, 0, -2) . ";";

		if ($this->query($query)) {
			return $this->lastInsertID();
		} else {
			return 0;
		}
	}

	function update($table, $array, $where) {
		$data = null;
		foreach($array as $key => $value) {
			$data .= sprintf("`%s` = %s, ", $key, $this->escape($value));
		}
		$data = substr($data, 0, -2);

		$conditions = null;
		foreach($where as $key => $value) {
			$conditions .= sprintf("`%s` = %s AND ", $key, $this->escape($value));
		}
		$conditions = substr($conditions, 0, -5);

		$query = sprintf("UPDATE `%s` SET %s WHERE %s", $table, $data, $conditions);

		return $this->query($query);
	}

	function delete($table, $data) {
		if (is_array($data)) {
			$query = "DELETE FROM `{$table}` WHERE ";
			foreach($data as $key => $value) {
				$query .= sprintf("`%s` = %s AND ", $key, $this->escape($value));
			}
			$query = substr($query, 0, -5);
		} else {
			$query = sprintf("DELETE FROM `%s` WHERE `id` = %s", $table, $this->escape($data));
		}
		return $this->query($query);
	}

	function log($type, $level, $event) {
		$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
		$query = sprintf("INSERT INTO `history` (`user_id`, `ip_address`, `type`, `level`, `event`)
			VALUES (%d, '%s', '%s', '%s', %s)",
		$user_id, $_SERVER['REMOTE_ADDR'], $type, $level, $this->escape($event));
		return $this->query($query);
	}

	// Total data set length
	function datatablesTotal($sFrom, $sWhere) {
		$ps = $this->prepare("SELECT COUNT(*) FROM $sFrom WHERE $sWhere");
		$ps->execute();
		return $ps->fetch(PDO::FETCH_COLUMN);
	}

	function datatablesPaging() {
		$sLimit = "";
		if (isset($_GET['iDisplayStart']) && isset($_GET['iDisplayLength']) && $_GET['iDisplayLength'] != '-1') {
			$sLimit = "LIMIT ".  $this->escape($_GET['iDisplayStart'], false).", ".
			$this->escape($_GET['iDisplayLength'], false);
		}
		return $sLimit;
	}

	function datatablesOrdering($aColumns) {
		$sOrder = "";
		if (isset($_GET['iSortCol_0'])) {
			$sOrder = "ORDER BY ";
			for ($i=0 ; $i<intval(@$_GET['iSortingCols']) ; $i++) {
				if (@$_GET['bSortable_'.intval(@$_GET['iSortCol_'.$i])] == "true") {
					$sOrder .= "`".$aColumns[intval(@$_GET['iSortCol_'.$i])]."`
				 	".$this->escape(@$_GET['sSortDir_'.$i], false) .", ";
				}
			}
			$sOrder = substr_replace($sOrder, "", -2);
			if ($sOrder == "ORDER BY") {
				$sOrder = "";
			}
		}
		return $sOrder;
	}

	function datatablesFiltering($aColumns) {
		$sWhere = "";
		if (@$_GET['sSearch'] != "") {
			$sWhere .= " (";
			for ($i=0 ; $i<count($aColumns) ; $i++) {
				$sWhere .= $aColumns[$i]." LIKE '%".$this->escape($_GET['sSearch'], false)."%' OR ";
			}
			$sWhere = substr_replace($sWhere, "", -3);
			$sWhere .= ')';
		}
		return $sWhere;
	}

	// Output array, to be converted in JSON
	function datatablesOutput($iTotal, $iFilteredTotal, $aaData) {
		return array(
			'sEcho' => (int) @$_GET['sEcho'],
			'iTotalRecords' => (int) $iTotal,
			'iTotalDisplayRecords' => (int) $iFilteredTotal,
			'aaData' => $aaData
		);
	}

}

?>