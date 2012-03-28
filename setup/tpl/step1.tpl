{extends file="components/main.tpl"}

{block name="content"}
<div class="page_title">Welcome to the MagIRC Setup!</div>
<p>Please follow the on-screen instructions to install MagIRC</p>

<pre>Checking PHP version...{if $status.php} <span style="color:green">Supported</span> ({$phpversion})</pre>
{else} <span style="color:red">Not Supported</span> ({$phpversion}) ><br />You need at least version 5.2.0</pre>{/if}

<pre>Checking PHP magic_quotes_gpc...{if $status.magic_quotes} <span style="color:orange;">Enabled</span><br />Please ensure that the magic_quotes_gpc option is turned OFF in your php.ini!</pre>
{else} <span style="color:green;">Disabled</span></pre>{/if}

<pre>Checking PHP PDO mysql driver... {if $status.pdo}<span style="color:green">Present</span></pre>
{else} <span style="color:red">Missing!</span><br />This component is required to run MagIRC. Please contact your Administrator.</pre>{/if}

<pre>Checking SQL configuration files...{if $status.writable} <span style="color:green;">Writable</span></pre>
{else} <span style="color:orange;">Not writable</span><br />Please copy the conf/*.cfg.dist.php files to conf/*.cfg.php and chmod them to 0666.<br />Alternatively, chmod the conf/ directory to 0777.<br />If it still doesn't work don't worry, you can continue anyway.</pre>{/if}

<pre>Checking if <em>tmp/compiled</em> is writable... {if $status.compiled} <span style="color:green;">Writable</span></pre>
{else} <span style="color:red;">Not writable</span><br />Please chmod the directory to 0777</pre>{/if}

<pre>Checking if <em>tmp/cache</em> is writable... {if $status.cache} <span style="color:green;">Writable</span></pre>
{else} <span style="color:red;">Not writable</span><br />Please chmod the directory to 0777</pre>{/if}
	
<pre>Checking if <em>admin/tmp</em> is writable... {if $status.admin} <span style="color:green;">Writable</span></pre>
{else} <span style="color:red;">Not writable</span><br />Please chmod the directory to 0777</pre>{/if}

{if !$status.error}

	<pre>Testing MagIRC Database connection... {if !$status.magirc_db}<span style="color:green;">Passed</span></pre>
	{elseif $status.magirc_db == 'new'}<span style="color:orange;">Unconfigured</span></pre>
		{include file="components/_db_form.tpl"}
	{else}<span style="color:red">{$status.magirc_db}</span>
		{include file="components/_db_form.tpl"}
	{/if}
	{if $smarty.post.savedb}
		{if $status.writable}
		<div class="configsave">Configuration saved</div>
		{else}
		<p><strong>Please replace the contents of the {$magirc_conf} file with the text below:</strong></p>
		<textarea name="sql_buffer" cols="64" rows="10" readonly="readonly">{$db_buffer}</textarea>
		<p>When you are done please <a href="?step=1">repeat this step</a></p>
		{/if}
	{/if}
	
	{if !$status.magirc_db}<p>Continue to the <a href="?step=2">next step</a></p>{/if}
{/if}
{/block}