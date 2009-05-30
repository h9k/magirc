<?php
// $Id$

class Magirc {
	var $db = null;
	var $tpl = null;
	var $denora = null;

	function Magirc() {
		$this->db =& new Magirc_DB;
		$this->denora =& new Denora_DB;
		$this->tpl =& new Magirc_Smarty;
	}
	
	// Gets and returns the given url parameter depending on what it is
	function getUrlParameter($param) {
		switch ($param) {
			case 'section':
				$param = isset($_GET['section']) ? $_GET['section'] : 'home';
				break;
			default:
				$param = NULL;
		}
		return stripslashes(htmlspecialchars(basename($param)));
	}
	
	//TODO: implement :)
	function getPage($page) {
		return NULL;
	}
}

?>