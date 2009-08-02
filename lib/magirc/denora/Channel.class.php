<?php
// $Id$

class Channel extends Denora {
	
	function Channel() {
		$this->db = new Denora_DB();
	}
	
	// return an array of all channels
	function getChannels() {
		$data = array(); $i = 0;
		$chans = $this->db->select('chan', array('*'), NULL, 'channel', 'ASC');
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
	
	function getChannel($name) {
		$chan = $this->db->select('chan', array('*'), array('channel' => $name));
		if ($chan) {
			$chan = $chan[0];
			$this->id = $chan['chanid'];
			$this->name = $chan['channel'];
			$this->users = $chan['currentusers'];
			$this->users_max = $chan['maxusers'];
			$this->users_max_time = $chan['maxusertime'];
			$this->topic = $chan['topic'];
			$this->topic_author = $chan['topicauthor'];
			$this->topic_time = strtotime($chan['topictime']);
			$this->kicks = $chan['kickcount'];
			$this->modes = $this->getModes($chan);
		}	
		return $this;
	}
	
	private function getModes($chan) {
		$modes = ""; $j = 97;
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