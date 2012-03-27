<?php
// Bahamut protocol file for Denora support on Magirc

class Protocol {
	private $oper_hidden_mode = 'H';
	private $helper_mode = '';
	private $bot_mode = '';
	private $services_protection_mode = '';
	private $chan_hide_mode = '';
	private $chan_secret_mode = 's';
	private $chan_private_mode = 'p';

	private $chan_exception = 1;
	private $chan_invites = 1;
	private $line_sq = 1;
	private $line_g = 0;
	private $host_cloaking = 0;

	function getParam($param) {
		return @$this->$param;
	}
}

?>