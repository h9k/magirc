<?php
// $Id$

class Status extends Denora {
	
	function Status() {
		$this->db = new Denora_DB();
		
		$current = $this->getCurrent();
		foreach ($current as $item) {
			$key = 'current_'.$item['type'];
			$key_time = $key.'_time';
			$this->$key = $item['val'];
			$this->$key_time = $item['time'];
		}
		
		$max = $this->getMax();
		foreach ($max as $item) {
			$key = 'max_'.$item['type'];
			$key_time = $key.'_time';
			$this->$key = $item['val'];
			$this->$key_time = strtotime($item['time']);
		}
		
	}
	
	function getCurrent() {
		$current = $this->db->select('current', array('*'));
		return @$current;
	}
	
	function getMax() {
		$max = $this->db->select('maxvalues', array('*'));
		return @$max;
	}
	
}

?>