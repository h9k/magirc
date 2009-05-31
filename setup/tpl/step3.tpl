{* $Id$ *}
{include file="_header.tpl"}

<pre>Logging in...
{if $login}
	<span style="color:green;">Done</span></pre>
	<pre>Checking configuration table...
	{if !$check}
		Creating...
		{if !$result}
			<span style="color:green;">Done</span></pre>
		{else}
			<span style="color:red;">Failed</span></pre>
		{/if}
	{else}
		<span style="color:green;">OK</span> (version {$version})</pre>
	{/if}
{else}
	<span style="color:red;">Failed</span></pre>
	Please use a valid admin username and password, as specified in the denora server configuration file.<br />
	<a href="?step=2">Try again</a>
{/if}

{if !$error}
	<p>Setup finished! You <strong>MUST</strong> now logon into the <a href="../admin/"><strong>Admin Interface</strong></a> to configure Magirc</p>
{/if}

{include file="_footer.tpl"}