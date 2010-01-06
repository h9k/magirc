<?php
// $Id$

// define the query types
define('SQL_NONE', 1);
define('SQL_ALL', 2);
define('SQL_INIT', 3);

// define the query formats
define('SQL_ASSOC', PDO::FETCH_ASSOC);
define('SQL_INDEX', PDO::FETCH_NUM);
define('SQL_OBJ', PDO::FETCH_OBJ);

class DB {

	var $pdo = null;
	var $result = null;
	var $error = null;
	var $record = null;

	function SQL() { }

	function connect($dsn, $username, $password) {
		try {
			$this->pdo = new PDO($dsn, $username, $password);
			return true;
		} catch(PDOException $e) {
			$this->error = $e->getMessage();
			return false;
		}
	}

	function disconnect() {
		$this->pdo = null;
	}

	function numRows() {
		try {
			return $this->result->rowCount();
		} catch(PDOException $e) {
			die($e->getMessage());
		}
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
		if ($this->record = $this->result->fetch($format)) {
			return $this->record;
		} else {
			return false;
		}
	}

	function escape($input) {
		return $this->pdo->quote($input);
	}

	function lastInsertID() {
		return $this->pdo->lastInsertId();
	}

	private function select($table, $where = NULL, $sort = NULL, $order = 'ASC', $limit = 0, $type = SQL_ALL, $format = SQL_ASSOC) {
		$query = "SELECT * FROM `{$table}`";

		if ($where) {
			$conditions = NULL;
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
			$query .= sprintf("`%s` = %s, ", $key, str_replace('\r\n', '', $this->escape($value)));
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
			$data .= sprintf("`%s` = %s, ", $key, str_replace('\r\n', '', $this->escape($value)));
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

	function delete($table, $id) {
		$query = sprintf("DELETE FROM `%s` WHERE `id` = %d", $table, $this->escape($id));
		return $this->query($query);
	}

	function log($type, $level, $event) {
		$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
		$query = sprintf("INSERT INTO `history` (`user_id`, `type`, `level`, `event`)
			VALUES (%d, '%s', '%s', '%s')",
		$user_id, $type, $level, $event);
		return $this->query($query);
	}

}

?>