{* $Id$ *}
<p>Please configure the access to the Magirc SQL database</p>
<form id="database" name="database" method="post" action="?db=denora">
<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td align="right">Username</td>
		<td align="left"><input name="username" type="text" id="username"
			tabindex="1" value="{$db_denora.username}" size="32"
			maxlength="1024" /></td>
	</tr>
	<tr>
		<td align="right">Password</td>
		<td align="left"><input type="password" name="password" id="password"
			tabindex="2" value="{$db_denora.password}" size="32"
			maxlength="1024" /></td>
	</tr>
	<tr>
		<td align="right">Database Name</td>
		<td align="left"><input type="text" name="database" id="database"
			tabindex="3" value="{$db_denora.database}" size="32"
			maxlength="1024" /></td>
	</tr>
	<tr>
		<td align="right">Hostname</td>
		<td align="left"><input type="text" name="hostname" id="hostname"
			tabindex="4" value="{$db_denora.hostname}" size="32"
			maxlength="1024" /></td>
	</tr>
	<tr>
		<td align="right">TCP Port</td>
		<td align="left"><input type="text" name="port" id="port" tabindex="5"
			value="{$db_denora.port}" size="32" maxlength="1024" /></td>
	</tr>
	<tr>
		<td align="right">Server table</td>
		<td align="left"><input type="text" name="table_server"
			id="table_server" tabindex="6"
			value="{$config.table_server}" size="32"
			maxlength="1024" /></td>
	</tr>
</table>
<p align="right"><input type="submit" name="button" id="button"
	value="Continue" tabindex="8" /></p>
</form>