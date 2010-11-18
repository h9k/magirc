<?php
// $Id$

// Inspircd 1.1/1.2 protocol file for Denora support on Magirc

class Protocol {
	public $oper_hidden_mode = 'H';
	public $helper_mode = 'h';
	public $bot_mode = 'B';
	public $services_protection_mode = '';
	public $chan_hide_mode = 'I';
	public $chan_secret_mode = 's';
	public $chan_public_mode = 'p';

	public $chan_exception = 1;
	public $chan_invites = 1;
	public $line_sq = 0;
	public $line_g = 1;
	public $host_cloaking = 1;

	function __construct() {
		;
	}

	function getParam($param) {
		return @$this->$param;
	}
}

?>