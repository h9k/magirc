<?php
// Inspircd 1.2/2.x protocol file for Denora support on Magirc

class Protocol {
	const ircd = 'inspircd';

	const chan_modes = 'cfgijklmnprstuzCFGJKLMNOPQRSTV';
	const chan_modes_data = 'fjklFLJ';
	const user_modes = 'cdghinorswxBGHIQRSW';
	
	const oper_hidden_mode = 'H';
	public static $oper_levels = array();
	const helper_mode = 'h';
	const bot_mode = 'B';
	const services_protection_mode = '';
	const chan_hide_mode = 'I';
	const chan_secret_mode = 's';
	const chan_private_mode = 'p';

	const chan_exception = true;
	const chan_invites = true;
	const line_sq = false;
	const line_g = true;
	const host_cloaking = true;
}

?>