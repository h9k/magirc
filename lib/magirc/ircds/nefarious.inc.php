<?php
// Nefarious 13 protocol file for Denora support on Magirc

class Protocol {
	const ircd = 'nefarious';
	
	const chan_modes = 'aciklmnprstzCLMNOQSTZ';
	const chan_modes_data = 'klL';
	const user_modes = 'acdfghiknoqrsxwzBCDHILORWX';

	const oper_hidden_mode = 'H';
	public static $oper_levels = array();
	const helper_mode = '';
	const bot_mode = 'B';
	const services_protection_mode = 'k';
	const chan_hide_mode = 'n';
	const chan_secret_mode = 's';
	const chan_private_mode = 'p';

	const chan_exception = true;
	const chan_invites = false;
	const line_sq = false;
	const line_g = false;
	const host_cloaking = true;
}

?>