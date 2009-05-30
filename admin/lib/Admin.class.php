<?php
// $Id$

class Admin {
	
	function Admin() {
		;
	}

	/* Saves the given configuration parameter and value */
	function saveConfig($parameter, $value){
		return $this->db->update('magirc_config', array('value' => $value), array('parameter' => $parameter));
	}
}

?>