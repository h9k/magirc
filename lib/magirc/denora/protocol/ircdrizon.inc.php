<?php
// ircd-rizon protocol file for Denora support on Magirc

class Protocol {
	const ircd = 'ircdrizon';
	
	const chan_modes = 'ciklmnpstzBMNORSZ';
	const chan_modes_data = 'kl';
	const user_modes = 'abcdfgijklnopqrsuwxyzCDGNRSWX';

	const oper_hidden_mode = '';
	public static $oper_levels = array();
	const helper_mode = '';
	const bot_mode = '';
	const services_protection_mode = '';
	const chan_hide_mode = '';
	const chan_secret_mode = 's';
	const chan_private_mode = 'p';

	const chan_exception = true;
	const chan_invites = true;
	const line_sq = true;
	const line_g = true;
	const host_cloaking = true;
}

?>