<h1>Registration</h1>
<form id="registration" method="post" action="http://denorastats.org/register.php">
	<input type="hidden" name="form" value="registration" />
	<table border="0" cellspacing="2" cellpadding="2">
		<tr>
			<td align="right"><strong>Network Name:</strong></td>
			<td>{$cfg.net_name}</td>
		</tr>
		<tr>
			<td align="right"><strong>Network Homepage URL: </strong></td>
			<td>{$cfg.net_url}</td>
		</tr>
		<tr>
			<td align="right"><strong>MagIRC URL: </strong></td>
			<td>http://{$magirc_url}</td>
		</tr>
		<tr>
			<td align="right"><strong>MagIRC Version: </strong></td>
			<td>{$smarty.const.VERSION_FULL}</td>
		</tr>
		<tr>
			<td align="right"><strong>Email address:</strong></td>
			<td><input name="email" type="text" size="32" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="hidden" name="netname" value="{$cfg.net_name}" />
				<input type="hidden" name="netpage" value="{$cfg.net_url}" />
				<input type="hidden" name="homepage" value="http://{$server.HTTP_HOST}{$server.REQUEST_URI}" />
				<input type="hidden" name="version" value="{$smarty.const.VERSION_FULL}" />
				<input type="hidden" name="ircd" value="{$cfg.ircd_type}" />
			</td>
		</tr>
	</table>
	<input type="submit" value="Register" />
</form>