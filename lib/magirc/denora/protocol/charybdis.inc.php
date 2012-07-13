<?php
// Charybdis protocol file for Denora support on Magirc

class Protocol {
	const ircd = 'charybdis';
	
	const chan_modes = 'cfgijklmnprstzFLPQ';
	const chan_modes_data = 'fjkl';
	const user_modes = 'ahgiloswzQRSZ';

	const oper_hidden_mode = '';
	public static $oper_levels = array();
	const helper_mode = '';
	const bot_mode = '';
	const services_protection_mode = 'S';
	const chan_hide_mode = '';
	const chan_secret_mode = 's';
	const chan_private_mode = '';

	const chan_exception = true;
	const chan_invites = true;
	const line_sq = false;
	const line_g = true;
	const host_cloaking = true;
}

?>