<?php
// Unreal 4.0 protocol file for Magirc

class Protocol {
	const ircd = 'unreal40';

	const chan_modes = 'cdfiklmnprstzCDGKLMNOPQRSTVZ';
	const chan_modes_data = 'fklL';
	const user_modes = 'diopqrstvwxzBGHIRSTVW';

	const oper_hidden_mode = 'H';
	public static $oper_levels = array(
		'o' => 'IRC Operator'
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