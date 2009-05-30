<?php
// $Id$

// define the query types
define('SQL_NONE', 1);
define('SQL_ALL', 2);
define('SQL_INIT', 3);

// define the query formats
define('SQL_ASSOC', 1);
define('SQL_INDEX', 2);

//define the sorting shit
define('SQL_ASC', 'ASC');
define('SQL_DESC', 'DESC');

class DB {

	var $db = null;
	var $result = null;
	var $error = null;
	var $record = null;

	/**
	 * class constructor
	 */
	function SQL() { }

	/**
	 * connect to the database
	 *
	 * @param string $dsn the data source name
	 */
	function connect($dsn) {
		$this->db = MDB2::connect($dsn);

		if(MDB2::isError($this->db)) {
			$this->error = $this->db->getMessage();
			return false;
		}
		
		
		
		return true;
	}

	/**
	 * disconnect from the database
	 */
	function disconnect() {
		$this->db->disconnect();
	}
	
	/*function numRows() {
		$test = $this->result->numRows();
		echo "CAZZO";
		if (MDB2::isError($test)) {
			echo "<pre>" . $this->result->numRows()->getMessage() . "</pre>";
			return false;
		} else {
			//return $test;
			return true;		
		}
	}*/

	/**
	 * query the database
	 *
	 * @param string $query the SQL query
	 * @param string $type the type of query
	 * @param string $format the query format
	 */
	function query($query, $type = SQL_NONE, $format = SQL_INDEX) {

		$this->record = array();
		$_data = array();

		// determine fetch mode (index or associative)
		$_fetchmode = ($format == SQL_ASSOC) ? MDB2_FETCHMODE_ASSOC : null;

		$this->result = $this->db->query($query);
		if (MDB2::isError($this->result)) {
			$this->error = $this->result->getMessage();
			//$this->log('system', 'error', sprintf("%s (Query: %s)", $this->error, $query)); //infinite loop if error is in log query!
			//$this->log('system', 'error', $this->error); //infinite loop if error is in log query!
			die("<pre>".$query."</pre><pre>".$this->error."</pre>"); //temporary!
			return false;
		}
		switch ($type) {
			case SQL_ALL:
				// get all the records
				while($_row = $this->result->fetchRow($_fetchmode)) {
					$_data[] = $_row;
				}
				$this->result->free();
				$this->record = $_data;
				break;
			case SQL_INIT:
				// get the first record
				$this->record = $this->result->fetchRow($_fetchmode);
				break;
			case SQL_NONE:
			default:
				// records will be looped over with next()
				break;
		}
		return true;
	}

	/**
	 * connect to the database
	 *
	 * @param string $format the query format
	 */
	function next($format = SQL_INDEX) {
		// fetch mode (index or associative)
		$_fetchmode = ($format == SQL_ASSOC) ? MDB2_FETCHMODE_ASSOC : null;
		if (MDB2::isError($this->result)) {
			$this->error = $this->result->getMessage();
			return false;
		}
		if ($this->record = $this->result->fetchRow($_fetchmode)) {
			return true;
		} else {
			$this->result->free();
			return false;
		}

	}
	
	function escape($input) {
		return $this->db->escape($input);
	}
	
	function lastInsertID() { 
		return $this->db->lastInsertID(); 
	}
	
	
	function get($table, $what, $where = NULL, $orderby = NULL, $sort = SQL_ASC, $limit = NULL) {
		$data = NULL;
		foreach($what as $value) {
			$data .= sprintf("`%s`, ", $this->escape($value));
		}
		$data = substr($data, 0, -2);
		
		$conditions = NULL;
		if ($where) {
			foreach($where as $key => $value) {
				$conditions .= sprintf("`%s` = '%s' AND ", $this->escape($key), $this->escape($value));
			}
			$conditions = substr($conditions, 0, -5);
		}
		
		$query = sprintf("SELECT %s FROM `%s`", $data, $table);
		if ($conditions) {
			$query .= sprintf(" WHERE %s", $conditions);
		}
		if ($orderby) {
			$query .= sprintf(" ORDER BY `%s` %s", $orderby, $sort);
		}
		if ($limit) {
			$query .= sprintf(" LIMIT %s", $limit);
		}
		$this->query($query, SQL_ALL, SQL_ASSOC);
		return $this->record;
	}
	
	/* Insert Array into given Table */
	function insert($table, $array) {
		$query = sprintf("INSERT INTO `%s` SET ", $table);
		
		foreach($array as $key => $value) {
			$query .= sprintf("`%s` = '%s', ", $this->escape($key), str_replace('\r\n', '', $this->escape($value)));
		}
		
		$query = substr($query, 0, -2) . ";";
		
		if ($this->query($query)) {
			$lastID = $this->lastInsertID();
			if (MDB2::isError($lastID)) {
				//$this->log('system', 'error', sprintf("%s (Query: %s)", $lastID->getMessage(), $query));
				die($lastID->getMessage());
			} else {
				return $lastID;
			}
		} else {
			return 0;
		}
	}
	
	function update($table, $array, $where) {
		$data = null;
		foreach($array as $key => $value) {
			$data .= sprintf("`%s` = '%s', ", $this->escape($key), str_replace('\r\n', '', $this->escape($value)));
		}
		$data = substr($data, 0, -2);
		
		$conditions = null;
		foreach($where as $key => $value) {
			$conditions .= sprintf("`%s` = '%s' AND ", $this->escape($key), $this->escape($value));
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
	
	function select($table, $what = array('*'), $where = NULL, $sort = NULL, $order = 'ASC', $limit = 0) {	
		$columns = null;
		foreach($what as $value) {
			$columns .= sprintf("`%s`, ", $this->escape($value));
		}
		
		$query = sprintf("SELECT %s FROM `%s`", substr($columns, 0, -2), $table);
		if ($where) {
			$conditions = null;
			foreach($where as $key => $value) {
				$conditions .= sprintf("`%s` = '%s' AND ", $this->escape($key), $this->escape($value));
			}
			$query .= sprintf(" WHERE %s", substr($conditions, 0, -5));
		}
		if ($sort) {
			$query .= sprintf(" ORDER BY `%s` %s", $this->escape($sort), $this->escape($order));
		}
		if ($limit) {
			$query .= sprintf(" LIMIT %s", $this->escape($limit));
		}
		
		$this->query($query, SQL_ALL, SQL_ASSOC);
		
		return $this->record;
	}

}

?>