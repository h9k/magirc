<?php
// $Id$

class Magirc {
	var $db = null;
	var $tpl = null;
	var $denora = null;

	function Magirc() {
		$this->db =& new Magirc_DB;
		$this->tpl =& new Magirc_Smarty;
		$this->denora =& new Denora_DB;
	}
	
	// Gets and returns the given url parameter depending on what it is
	function getUrlParameter($param) {
		switch ($param) {
			case 'section':
				$param = isset($_GET['section']) ? $_GET['section'] : 'home';
				break;
			case 'action':
				$param = isset($_GET['action']) ? $_GET['action'] : 'main';
				break;
			default:
				$param = isset($_GET[$param]) ? $_GET[$param] : '';
		}
		return stripslashes(htmlspecialchars(basename($param)));
	}
	
	function displayError($err_msg) {
		$this->tpl->assign('err_msg', $err_msg);
		$this->tpl->display('error.tpl');
	}
	
	//TODO: implement :)
	function getPage($page) {
		return NULL;
	}
}

?>