{* $Id$ *}
{include file="_header.tpl"}
<div class="page_title">Welcome to the Magirc Setup!</div>
<p>Please follow the on-screen instructions to install Magirc</p>

<pre>Checking PHP version...{if $status.php} <span style="color:green">Supported</span> ({$phpversion})</pre>
{else} <span style="color:red">Not Supported</span> ({$phpversion}) ><br />You need at least version 5.2.0</pre>{/if}

<pre>Checking PHP MySQLi extension... {if $status.mysqli}<span style="color:green">Present</span></pre>
{else} <span style="color:red">Missing!</span><br />This component is required to run Magirc. Please contact your Administrator.</pre>{/if}

<pre>Checking PHP GD extension... {if $status.gd}<span style="color:green">Present</span></pre>
{else} <span style="color:red">Missing!</span><br />This component is required to run Magirc. Please contact your Administrator.</pre>{/if}

<pre>Checking SQL configuration files...{if $status.writable} <span style="color:green;">Writable</span></pre>
{else} <span style="color:orange;">Not writable</span><br />Please ensure that the $magirc_conf and $denora_conf files have enough write permissions.<br />Try chmod 0666 or 0777. If it still doesn't work don't worry, you can continue anyway.</pre>{/if}

<pre>Checking if <em>tmp/compiled</em> is writable... {if $status.compiled} <span style="color:green;">Writable</span></pre>
{else} <span style="color:red;">Not writable</span><br />Please chmod the directory to 0777</pre>{/if}

<pre>Checking if <em>tmp/cache</em> is writable... {if $status.cache} <span style="color:green;">Writable</span></pre>
{else} <span style="color:red;">Not writable</span><br />Please chmod the directory to 0777</pre>{/if}

{if !$status.error}

	<pre>Testing Magirc Database connection... {if !$status.magirc_db}<span style="color:green;">Passed</span></pre>
	{else} <span style="color:red">{$status.magirc_db}</span>
		{include file="_db_magirc.tpl"}
	{/if}
	{if $smarty.post.button}
		{if $status.writable}
		<div class="configsave">Configuration saved</div>
		{else}
		<p><strong>Please replace the contents of the $magirc_conf file with the text below:</strong></p>
		<textarea name="sql_buffer" cols="64" rows="10" readonly="readonly">{$db_buffer}</textarea>
		<p>When you are done please <a href="?step=1">repeat this step</a></p>
		{/if}
	{/if}
	
	<pre>Testing Denora Database connection... {if !$status.denora_db}<span style="color:green;">Passed</span></pre>
	{else} <span style="color:red">{$status.denora_db}</span>
		{include file="_db_denora.tpl"}
	{/if}
	{if $smarty.post.button}
		{if $status.writable}
		<div class="configsave">Configuration saved</div>
		{else}
		<p><strong>Please replace the contents of the $denora_conf file with the text below:</strong></p>
		<textarea name="sql_buffer" cols="64" rows="10" readonly="readonly">{$db_buffer}</textarea>
		<p>When you are done please <a href="?step=1&amp;table_server={$config.table_server}">repeat this step</a></p>
		{/if}
	{/if}
	
	{if !$status.denora_db && !$status.magirc_db}<p>Continue to the <a href="?step=2&amp;table_server={$config.table_server}">next step</a></p>{/if}
{/if}

{include file="_footer.tpl"}