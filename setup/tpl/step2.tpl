{extends file="components/main.tpl"}

{block name="content"}
<pre>Checking MagIRC database schema...
{if !$check}
	Creating...
	{if $dump}
		<span style="color:green;">Done</span></pre>
	{else}
		<span style="color:red;">Failed</span></pre>
	{/if}
{else}
	<span style="color:green;">OK</span> (version {$version})</pre>
{/if}
{if $check && ($dump || $version)}
	{if !$admins}
		<p>You must now create a MagIRC admin user</p>
		<form id="login" method="post" action="?step=3">
		<table width="350" border="0" cellpadding="2" cellspacing="0">
			<tr>
			<td style="width:100px;"><label>User</label></td>
			<td style="width:150px;"><input type="text" name="username" id="username" tabindex="1" /></td>
			<td style="width:50px;" rowspan="2" align="center" valign="middle"><input type="submit" name="login" value="Create" tabindex="3" /></td>
			</tr>
			<tr>
			<td style="width:100px;"><label>Password</label></td>
			<td style="width:150px;"><input type="password" name="password" id="password" tabindex="2" /></td>
			</tr>
		</table>
		</form>
	{else}
		<p>Setup finished!<br />You <strong>MUST</strong> now logon into the <a href="../admin/"><strong>Admin Interface</strong></a> to configure MagIRC, especially the Denora database</p>
	{/if}
{/if}
{/block}