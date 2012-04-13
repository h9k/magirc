{extends file="_main.tpl"}

{block name="content"}
<h2>2. Database check</h2>

<pre>Testing MagIRC Database connection...
{if $status.error}
	{if $status.error == 'new'}<span style="color:orange;">Unconfigured</span>
	{else}<span style="color:red">{$status.error}</span>
	{/if}
	</pre>
	<p>Please configure the access to the MagIRC SQL database
	<br />You can use an existing database (for example the Denora database) if you like, or use a separate one.</p>
	<form name="database" method="post" action="?step=2">
	<table class="form">
		<tr>
			<th><label for="username">Username</label></th>
			<td><input name="username" type="text" id="username" value="{$db_magirc.username}" size="32" maxlength="128" /></td>
		</tr>
		<tr>
			<th><label for="password">Password</label></th>
			<td><input type="password" name="password" id="password" value="{$db_magirc.password}" size="32" maxlength="128" /></td>
		</tr>
		<tr>
			<th><label for="database">Database Name</label></th>
			<td><input type="text" name="database" id="database" value="{$db_magirc.database}" size="32" maxlength="128" /></td>
		</tr>
		<tr>
			<th><label for="hostname">Hostname</label></th>
			<td><input type="text" name="hostname" id="hostname" value="{$db_magirc.hostname}" size="32" maxlength="128" /></td>
		</tr>
		<tr>
			<th><label for="port">TCP Port</label></th>
			<td><input type="text" name="port" id="port" tabindex="5" value="{$db_magirc.port}" size="32" maxlength="16" /></td>
		</tr>
	</table>
	<button type="submit" name="savedb" id="button">Save</button>
	</form>
{else}
	<span style="color:green;">Passed</span></pre>

	{if isset($smarty.post.savedb)}
		<pre>Saving configuration to file...
		{if !$status.writable}
		<span style="color:red;">Failed</span></pre>
		<p><strong>Please replace the contents of the {$smarty.const.MAGIRC_CFG_FILE} file with the text below:</strong></p>
		<textarea name="sql_buffer" cols="64" rows="8" readonly="readonly">{$db_buffer}</textarea>
		<p>Once you are done, please <a href="?step=2">repeat this step</a></p>
		{else}
		<span style="color:green;">Saved</span></pre>
		{/if}
	{/if}

	{if !isset($smarty.post.savedb) or $status.writable}
		<pre>Checking MagIRC database schema...
		{if !$check} Creating...
			{if $dump}<span style="color:green;">Done</span>{else}<span style="color:red;">Failed</span>{/if}
		{elseif $updated}
		<span style="color:green;">Upgraded</span> (version {$version})
		{else}
		<span style="color:green;">OK</span> (version {$version})
		{/if}
		</pre>

		{if $dump or $check}<button class="next">Continue</button>{/if}
	{/if}

{/if}

{/block}