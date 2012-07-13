<?php
// Bahamut protocol file for Denora support on Magirc

class Protocol {
	const ircd = 'bahamut';
	
	const chan_modes = 'cijklmnprstLMOR';
	const chan_modes_data = 'jkl';
	const user_modes = 'abcdefghijkmnorswxyADFIKORX';

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
	const line_g = false;
	const host_cloaking = false;
}

?>