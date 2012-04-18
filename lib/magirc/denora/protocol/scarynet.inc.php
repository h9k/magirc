<?php
// Scarynet protocol file for Denora support on Magirc

class Protocol {
	const ircd = 'scarynet';
	
	const chan_modes = 'ciklmnprstuCDNOT';
	const chan_modes_data = 'kl';
	const user_modes = 'dghikorswxBCHORW';

	const oper_hidden_mode = '';
	const helper_mode = 'H';
	const bot_mode = 'B';
	const services_protection_mode = '';
	const chan_hide_mode = '';
	const chan_secret_mode = 's';
	const chan_private_mode = 'p';

	const chan_exception = false;
	const chan_invites = false;
	const line_sq = false;
	const line_g = false;
	const host_cloaking = true;
}

?>