<?php
// IRCu protocol file for Denora support on Magirc

class Protocol {
	const ircd = 'ircu';
	
	const chan_modes = 'iklmnprst';
	const chan_modes_data = 'kl';
	const user_modes = 'dgikorsxw';

	const oper_hidden_mode = '';
	public static $oper_levels = array();
	const helper_mode = '';
	const bot_mode = '';
	const services_protection_mode = 'k';
	const chan_hide_mode = '';
	const chan_secret_mode = 's';
	const chan_private_mode = 'p';

	const chan_exception = false;
	const chan_invites = false;
	const line_sq = false;
	const line_g = false;
	const host_cloaking = false;
}

?>