<h1>Network settings</h1>
<form id="network-form">
	<table border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td align="right">The <strong>name of your Network</strong></td>
			<td align="left"><input name="net_name" type="text" id="net_name" value="{$cfg.net_name}" size="32" maxlength="1024" /></td>
		</tr>
		<tr>
			<td align="right">The URL of the <strong>Homepage of your Network</strong></td>
			<td align="left"><input name="net_url" type="text" id="net_url" value="{$cfg.net_url}" size="32" maxlength="1024" /></td>
		</tr>
		<tr>
			<td align="right"><strong>IRCd Server Type</strong></td>
			<td align="left">
				<select name="ircd_type" id="ircd_type">
				{foreach from=$ircds item=item}
					<option value="{$item}"{if $cfg.ircd_type eq $item} selected="selected"{/if}>{$item}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"><strong>Hide Ulined Servers</strong></td>
			<td align="left"><input name="hide_ulined" type="checkbox" id="hide_ulined" {if $cfg.hide_ulined}checked="checked" {/if}/></td>
		</tr>
		<tr>
			<td align="right">Servers you don't want MagIRC to show.<br />Separate with commas, example: &quot;hub.mynet.tld,hub2.mynet.tld&quot;</td>
			<td align="left"><input name="hide_servers" type="text" id="hide_servers" value="{$cfg.hide_servers}" size="32" maxlength="1024" /></td>
		</tr>
		<tr>
			<td align="right">Channels you don't want MagIRC to show.<br />Separate with commas, example: &quot;#opers,#services&quot;</td>
			<td align="left"><input name="hide_chans" type="text" id="hide_chans" value="{$cfg.hide_chans}" size="32" maxlength="1024" /></td>
		</tr>
	</table>
	<button id="network-submit" type="button">Save</button>
</form>

{jsmin}
<script type="text/javascript"><!--{literal}
$(function() {
	$("#network-submit").button().click(function() {
		$("#network-form").ajaxSubmit({ url: 'index.php/configuration', type: 'post', success: function(data) {
			if (data) $("#success").show().delay(1500).fadeOut(500);
			else $("#failure").show().delay(1500).fadeOut(500);
		} });
	});
});
{/literal}
--></script>
{/jsmin}