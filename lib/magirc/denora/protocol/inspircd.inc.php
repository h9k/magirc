<?php


// Inspircd 1.1/1.2/2.0 protocol file for Denora support on Magirc

class Protocol {
	private $oper_hidden_mode = 'H';
	private $helper_mode = 'h';
	private $bot_mode = 'B';
	private $services_protection_mode = '';
	private $chan_hide_mode = 'I';
	private $chan_secret_mode = 's';
	private $chan_private_mode = 'p';

	private $chan_exception = 1;
	private $chan_invites = 1;
	private $line_sq = 0;
	private $line_g = 1;
	private $host_cloaking = 1;

	function __construct() {
		;
	}

	function getParam($param) {
		return @$this->$param;
	}
}

?>