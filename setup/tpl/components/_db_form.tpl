{* $Id$ *}
<p>Please configure the access to the MagIRC SQL database</p>
<form id="database" name="database" method="post" action="">
<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td align="right">Username</td>
		<td align="left"><input name="username" type="text" id="username" value="{$db_magirc.username}" size="32" maxlength="128" /></td>
	</tr>
	<tr>
		<td align="right">Password</td>
		<td align="left"><input type="password" name="password" id="password" value="{$db_magirc.password}" size="32" maxlength="128" /></td>
	</tr>
	<tr>
		<td align="right">Database Name</td>
		<td align="left"><input type="text" name="database" id="database" value="{$db_magirc.database}" size="32" maxlength="128" /></td>
	</tr>
	<tr>
		<td align="right">Hostname</td>
		<td align="left"><input type="text" name="hostname" id="hostname" value="{$db_magirc.hostname}" size="32" maxlength="128" /></td>
	</tr>
	<tr>
		<td align="right">TCP Port</td>
		<td align="left"><input type="text" name="port" id="port" tabindex="5" value="{$db_magirc.port}" size="32" maxlength="16" /></td>
	</tr>
</table>
<p align="right"><input type="submit" name="savedb" id="button" value="Save" /></p>
</form>
