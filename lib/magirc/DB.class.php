<?php

// define the query types
define('SQL_NONE', 1);
define('SQL_ALL', 2);
define('SQL_INIT', 3);

// define the query formats
define('SQL_ASSOC', PDO::FETCH_ASSOC);
define('SQL_INDEX', PDO::FETCH_NUM);
define('SQL_OBJ', PDO::FETCH_OBJ);
define('SQL_NAME', PDO::FETCH_NAMED);

// define the parameter formats
define('SQL_NULL', PDO::PARAM_NULL);
define('SQL_BOOL', PDO::PARAM_BOOL);
define('SQL_INT', PDO::PARAM_INT);
define('SQL_STR', PDO::PARAM_STR);

class DB {
	private $pdo;
	private $result;
	public $error;
	public $record;

	function __construct($dsn, $username, $password, $args = null) {
		$this->connect($dsn, $username, $password, $args);
    }

	function __destruct() {
		$this->disconnect();
	}

	/**
	 * Establish a connection to a Database
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 * @return boolean true: successful, false: failed
	 */
	function connect($dsn, $username, $password, $args) {
		$limit = 5;
		$counter = 0;
		while (true) {
			try {
				$args[PDO::ATTR_PERSISTENT] = true;
				$this->pdo = new PDO($dsn, $username, $password, $args);
				$this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->pdo->query("SET NAMES utf8");
				return true;
			} catch (Exception $e) {
				if($e->getCode() == 2) {
					$this->pdo = null;
					$counter++;
					if ($counter >= $limit) {
						$this->error = $e->getMessage();
						return false;
					}
				} else {
					$this->error = $e->getMessage();
					return false;
				}
			}
		}
	}

	/**
	 * Disconnect from the database server
	 */
	function disconnect() {
		$this->pdo = null;
	}

	/**
	 * Get the tables
	 * @return array
	 */
	function getTables() {
		$query = "SHOW TABLES";
		$this->query($query, SQL_ALL, SQL_INDEX);
		return $this->record;
	}

	/**
	 * Create a prepared statement
	 * @param string $query
	 * @return PDOStatement
	 */
	function prepare($query) {
		return $this->pdo->prepare($query);
	}

	/**
	 * Runs the given query
	 * @param string $query
	 * @param int $type SQL_NONE: without result, SQL_INIT: one result, SQL_ALL: all results
	 * @param int $format SQL_INDEX: indexed array, SQL_ASSOC: associative array, SQL_OBJ object
	 * @return boolean true: success, false: failure
	 */
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

	/**
	 * Iterate over the next record
	 * @param int $format SQL_INDEX: indexed array, SQL_ASSOC: associative array, SQL_OBJ object
	 * @return mixed record on success, false on failure
	 */
	function next($format = SQL_INDEX) {
		$this->record = $this->result->fetch($format);
		if ($this->record) {
			return $this->record;
		} else {
			return false;
		}
	}

	/**
	 * Escape the given string
	 * @param string $input
	 * @return string Escaped string
	 */
	function escape($input) {
		return $this->pdo->quote($input);
	}

	/**
	 * Get the ID of the last inserted row
	 * @return int ID
	 */
	function lastInsertID() {
		return $this->pdo->lastInsertId();
	}

	/**
	 * Number of returned rows
	 * @return int Count
	 */
	function numRows() {
		try {
			return $this->result->rowCount();
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Fetch the first column of the last result
	 * @return mixed Value
	 */
	function fetchColumn() {
		try {
			return $this->result->fetchColumn();
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Return the amount of found rows from the last query
	 * @return int Rows
	 */
	function foundRows() {
		$ps = $this->prepare("SELECT FOUND_ROWS()");
		$ps->execute();
		return $ps->fetch(PDO::FETCH_COLUMN);
	}

	/**
	 * Build and run a SELECT query
	 * @param string $table Table
	 * @param array $where (column => value)
	 * @param string $sort ORDER BY ...
	 * @param string $order ASC/DESC
	 * @param int $limit LIMIT ...
	 * @param int $type SQL_NONE: without result, SQL_INIT: one result, SQL_ALL: all results
	 * @param int $format SQL_INDEX: indexed array, SQL_ASSOC: associative array, SQL_OBJ object
	 * @return mixed
	 */
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

	/**
	 * Build and run a SELECT query and return one row
	 * @param string $table Table
	 * @param array $where (column => value)
	 * @param string $sort ORDER BY ...
	 * @param string $order ASC/DESC
	 * @param int $limit LIMIT ...
	 * @return mixed
	 */
	function selectOne($table, $where = NULL, $sort = NULL, $order = 'ASC', $limit = 0) {
		return $this->select($table, $where, $sort, $order, $limit, SQL_INIT, $format = SQL_ASSOC);
	}
	/**
	 * Build and run a SELECT query and return all rows
	 * @param string $table Table
	 * @param array $where (column => value)
	 * @param string $sort ORDER BY ...
	 * @param string $order ASC/DESC
	 * @param int $limit LIMIT ...
	 * @return mixed
	 */
	function selectAll($table, $where = NULL, $sort = NULL, $order = 'ASC', $limit = 0) {
		return $this->select($table, $where, $sort, $order, $limit, SQL_ALL, $format = SQL_ASSOC);
	}

	/**
	 * Build and run an INSERT query
	 * @param string $table Table
	 * @param array $array Values (column => value)
	 * @return int Last inserted ID
	 */
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

	/**
	 * Build and run an UPDATE query
	 * @param string $table Table
	 * @param array $array Values (column => value)
	 * @param array $where WHERE (column => value)
	 * @return mixed
	 */
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

	/**
	 * Build and run a DELETE query
	 * @param string $table Table
	 * @param mixed $data int: id, array: (column => value)
	 * @return mixed
	 */
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

	/**
	 * Gate the total data set length (used by DataTables)
	 * @param string $sQuery SQL Query
	 * @param array $aParams (column => value)
	 * @return mixed
	 */
	function datatablesTotal($sQuery, $aParams = array()) {
		$sQuery = preg_replace('#SELECT\s.*?\sFROM#is', 'SELECT COUNT(*) FROM', $sQuery, 1);
		$ps = $this->prepare($sQuery);
		foreach ($aParams as $key => &$val) {
			$ps->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
		}
		$ps->execute();
		return $ps->fetch(PDO::FETCH_COLUMN);
	}

	/**
	 * Build the LIMIT portion of a query (used by DataTables)
	 * @return string LIMIT statement
	 */
	function datatablesPaging() {
		$sLimit = "";
		if (isset($_GET['iDisplayStart']) && isset($_GET['iDisplayLength']) && $_GET['iDisplayLength'] != '-1') {
			$sLimit = "LIMIT ". (int) $_GET['iDisplayStart'].", ". (int) $_GET['iDisplayLength'];
		}
		return $sLimit;
	}

	/**
	 * Build the ORDER BY portion of a query (used by DataTables)
	 * @param array $aColumns Column names
	 * @return string ORDER BY statement
	 */
	function datatablesOrdering($aColumns) {
		$sOrder = "";
		if (isset($_GET['iSortCol_0'])) {
			$sOrder = "ORDER BY ";
			for ($i=0 ; $i<intval(@$_GET['iSortingCols']) ; $i++) {
				$j = intval(@$_GET['iSortCol_'.$i]);
				if (@$_GET['bSortable_'.$j] == "true") {
					$sOrder .= "`".$aColumns[$j]."` ";
					if (isset($_GET['sSortDir_'.$i])) {
						$sOrder .= ($_GET['sSortDir_'.$i] == 'desc' ? 'desc' : 'asc') .", ";
					}
				}
			}
			$sOrder = substr_replace($sOrder, "", -2);
			if ($sOrder == "ORDER BY") {
				$sOrder = "";
			}
		}
		return $sOrder;
	}

	/**
	 * Build the WHERE portion of a query to filter results (used by DataTables)
	 * @param array $aColumns Column names
	 * @return string WHERE statement
	 */
	function datatablesFiltering($aColumns) {
		$sWhere = "";
		if (@$_GET['sSearch'] != "") {
			$sWhere .= " (";
			for ($i=0 ; $i<count($aColumns) ; $i++) {
				$sWhere .= $aColumns[$i]." LIKE ".$this->escape('%'.$_GET['sSearch'].'%')." OR ";
			}
			$sWhere = substr_replace($sWhere, "", -3);
			$sWhere .= ')';
		}
		return $sWhere;
	}

	/**
	 * Output the server-side array for DataTables, to be converted in JSON
	 * @param int $iTotal Total records
	 * @param int $iFilteredTotal Total displayed records
	 * @param array $aaData Data
	 * @return array (echo, total, displayed, data)
	 */
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