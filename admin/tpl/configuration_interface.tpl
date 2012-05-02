<form id="interface-form">
	<h1>Interface settings</h1>
	<table border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td align="right"><strong>Theme</strong><br />This only applies to the frontend</td>
			<td align="left">
				<select name="theme" id="theme">
				{foreach from=$themes item=item}
					<option value="{$item}"{if $cfg->theme eq $item} selected="selected"{/if}>{$item}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"><strong>Default Language</strong><br />Will not override automatic detection by browser</td>
			<td align="left">
				<select name="locale" id="locale">
				{foreach from=$locales item=item}
					<option value="{$item}"{if $cfg->locale eq $item} selected="selected"{/if}>{$item}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"><strong>Timezone</strong></td>
			<td align="left">
				<select name="timezone" id="timezone">
				{foreach from=$timezones item=item}
					<option value="{$item}"{if $cfg->timezone eq $item} selected="selected"{/if}>{$item}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"><strong>Enable CDN</strong><br />Improves performance</td>
			<td align="left">yes <input type="radio" name="cdn_enable" value="1"{if $cfg->cdn_enable} checked="checked"{/if} /> <input type="radio" name="cdn_enable" value="0"{if !$cfg->cdn_enable} checked="checked"{/if} /> no</td>
		</tr>
		<tr>
			<td align="right"><strong>Enable URL Rewrite</strong><br />Requires rewriting support on the web server.<br />For apache, please rename htaccess.txt to .htaccess</td>
			<td align="left">yes <input type="radio" name="rewrite_enable" value="1"{if $cfg->rewrite_enable} checked="checked"{/if} /> <input type="radio" name="rewrite_enable" value="0"{if !$cfg->rewrite_enable} checked="checked"{/if} /> no</td>
		</tr>
		<tr>
			<td align="right"><strong>Live update interval</strong><br />0 to disable</td>
			<td align="left"><input type="text" size="3" maxlength="3" name="live_interval" value="{$cfg->live_interval}" /> seconds</td>
		</tr>
		<tr>
			<td align="right"><strong>Show MagIRC version in footer</strong></td>
			<td align="left">yes <input type="radio" name="version_show" value="1"{if $cfg->version_show} checked="checked"{/if} /> <input type="radio" name="version_show" value="0"{if !$cfg->version_show} checked="checked"{/if} /> no</td>
		</tr>
		<tr>
			<td align="right"><strong>Debug mode</strong></td>
			<td align="left">
				<select name="debug_mode" id="debug_mode">
					<option value="0"{if $cfg->debug_mode eq '0'} selected="selected"{/if}>Off</option>
					<option value="1"{if $cfg->debug_mode eq '1'} selected="selected"{/if}>PHP Warnings/SQL Errors</option>
					<option value="2"{if $cfg->debug_mode eq '2'} selected="selected"{/if}>Verbose debugging</option>
				</select>
			</td>
		</tr>
	</table>
	<br /><button id="interface-submit" type="button">Save</button>
</form>

{jsmin}
<script type="text/javascript">{literal}
$(document).ready(function() {
	$("#interface-submit").button().click(function() {
		$("#interface-form").ajaxSubmit({ url: 'index.php/configuration', type: 'post', success: function(data) {
			if (data) $("#success").show().delay(1500).fadeOut(500);
			else $("#failure").show().delay(1500).fadeOut(500);
		} });
	});
});
{/literal}
</script>
{/jsmin}