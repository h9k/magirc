<?php
// Unreal 3.2 protocol file for Denora support on Magirc

class Protocol {
	const ircd = 'unreal32';
	
	const chan_modes = 'cfijklmnprstuzACGKLMNOQRSTV';
	const chan_modes_data = 'fjklL';
	const user_modes = 'adghiopqrstvwxzABCGHNORSTVW';

	const oper_hidden_mode = 'H';
	public static $oper_levels = array(
		'N' => 'Network Admin',
		'A' => 'Server Admin',
		'a' => 'Services Admin',
		'C' => 'Co-Admin',
		'o' => 'Global Operator'
	);
	const helper_mode = 'h';
	const bot_mode = 'B';
	const services_protection_mode = 'S';
	const chan_hide_mode = 'p';
	const chan_secret_mode = 's';
	const chan_private_mode = 'p';

	const chan_exception = true;
	const chan_invites = true;
	const line_sq = false;
	const line_g = true;
	const host_cloaking = true;
}

?>