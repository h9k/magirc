{extends file="_main.tpl"}

{block name="content"}
<h2>1. Requirements check</h2>

<pre>Checking PHP version...{if $status.php} <span style="color:green">Supported</span> ({$phpversion})</pre>
{else} <span style="color:red">Not Supported</span> ({$phpversion}) ><br />You need at least version 5.3.0</pre>{/if}

<pre>Checking PHP magic_quotes_gpc...{if $status.magic_quotes} <span style="color:red;">Enabled</span><br />Please ensure that the magic_quotes_gpc option is turned OFF in your php.ini!</pre>
{else} <span style="color:green;">Disabled</span></pre>{/if}

<pre>Checking PHP PDO mysql driver... {if $status.pdo}<span style="color:green">Present</span></pre>
{else} <span style="color:red">Missing!</span><br />This component is required to run MagIRC. Please contact your Administrator.</pre>{/if}

<pre>Checking PHP mcrypt extension... {if $status.mcrypt}<span style="color:green">Present</span></pre>
{else} <span style="color:red">Missing!</span><br />This component is required to run MagIRC. Please contact your Administrator.</pre>{/if}

<pre>Checking PHP gettext extension... {if $status.gettext}<span style="color:green">Present</span></pre>
{else} <span style="color:red">Missing!</span><br />This component is required to run MagIRC. Please contact your Administrator.</pre>{/if}

<pre>Checking SQL configuration files...{if $status.writable} <span style="color:green;">Writable</span></pre>
{else} <span style="color:orange;">Not writable</span>
	<br />Please copy the conf/*.cfg.dist.php files to conf/*.cfg.php<br />and chmod them to 0666.
	<br />Alternatively, chmod the conf/ directory to 0777.
	<br />If it still doesn't work don't worry, you can continue anyway.</pre>{/if}

<pre>Checking if <em>tmp/</em> is writable... {if $status.tmp} <span style="color:green;">Writable</span></pre>
{else} <span style="color:red;">Not writable</span><br />Please chmod the directory to 0777</pre>{/if}

{if !$status.error}<button class="next">Continue</button>{/if}

{/block}