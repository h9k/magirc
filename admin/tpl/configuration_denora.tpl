<h1>Database settings</h1>
<form id="denora-form">
<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td align="right">Username</td>
		<td align="left"><input name="username" type="text" id="username" value="{$db.username}" size="32" maxlength="64" /></td>
	</tr>
	<tr>
		<td align="right">Password</td>
		<td align="left"><input type="password" name="password" id="password" value="{$db.password}" size="32" maxlength="64" /></td>
	</tr>
	<tr>
		<td align="right">Database Name</td>
		<td align="left"><input type="text" name="database" id="database" value="{$db.database}" size="32" maxlength="64" /></td>
	</tr>
	<tr>
		<td align="right">Hostname</td>
		<td align="left"><input type="text" name="hostname" id="hostname" value="{$db.hostname}" size="32" maxlength="64" /></td>
	</tr>
	<tr>
		<td align="right">TCP Port</td>
		<td align="left"><input type="text" name="port" id="port" value="{$db.port}" size="32" maxlength="64" /></td>
	</tr>
	<tr>
		<td align="right">Enable SSL</td>
		<td align="left"><input type="checkbox" name="ssl" id="ssl" value="1" {if $db.ssl}checked="checked" {/if}/></td>
	</tr>
	<tr>
		<td align="right">Path to SSL Key</td>
		<td align="left"><input type="text" name="ssl_key" id="ssl_key" value="{$db.ssl_key}" size="32" maxlength="256" /></td>
	</tr>
	<tr>
		<td align="right">Path to SSL Certificate</td>
		<td align="left"><input type="text" name="ssl_cert" id="ssl_cert" value="{$db.ssl_cert}" size="32" maxlength="256" /></td>
	</tr>
	<tr>
		<td align="right">Path to SSL CA</td>
		<td align="left"><input type="text" name="ssl_ca" id="ssl_ca" value="{$db.ssl_ca}" size="32" maxlength="256" /></td>
	</tr>
		
</table>

<pre>The configuration file <em>{$db_config_file}</em> is {if $writable}<span style="color:green;">writable</span>
{else}<span style="color:red;">not writable</span><br />Please ensure that it has enough write permissions. Try chmod 0666 or 0777.
{if $smarty.post.button}
<br />Alternatively, please copy the following text<br />and paste it into the {$db_config_file} file:<br />
<textarea name="sql_buffer" cols="64" rows="10" readonly="readonly">{$db_buffer}</textarea>
{/if}
{/if}
</pre>

<button id="denora-submit" type="button">Save</button>
</form>

<div id="manual" style="display:none;">
	<br />MagIRC was unable to write the file.<br />Please create {$db_config_file} and paste the following code:
	<div id="file" class="file"></div>
</div>

{jsmin}
<script type="text/javascript">{literal}
$(document).ready(function() {
	$("#denora-submit").button().click(function() {
		$("#denora-form").ajaxSubmit({ url: 'index.php/configuration/denora/database', type: 'post', success: function(data) {
			if (data) $("#success").show().delay(1500).fadeOut(500);
			else {
				$("#failure").show().delay(1500).fadeOut(500);
				$("#manual").show();
				$("#file").html("<pre>&lt;?php\n"+
				"$db['username'] = \""+$("#username").val()+"\";\n"+
				"$db['password'] = \""+$("#password").val()+"\";\n"+
				"$db['database'] = \""+$("#database").val()+"\";\n"+
				"$db['hostname'] = \""+$("#hostname").val()+"\";\n"+
				"$db['port'] = \""+$("#port").val()+"\";\n"+
				"?&gt;<\/pre>");
			}
		} });
	});
});
{/literal}
</script>
{/jsmin}