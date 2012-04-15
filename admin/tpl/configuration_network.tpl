<h1>Network settings</h1>
<form id="network-form">
	<table border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td align="right">The <strong>name of your Network</strong></td>
			<td align="left"><input name="net_name" type="text" id="net_name" value="{$cfg.net_name}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right">The URL of the <strong>Homepage of your Network</strong></td>
			<td align="left"><input name="net_url" type="text" id="net_url" value="{$cfg.net_url}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right"><strong>The Round Robin for your Network</strong><br />For example irc.yourdomain.tld</td>
			<td align="left"><input name="net_roundrobin" type="text" id="net_roundrobin" value="{$cfg.net_roundrobin}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right"><strong>Default port or range</strong></td>
			<td align="left"><input name="net_port" type="text" id="net_port" value="{$cfg.net_port}" size="11" maxlength="11" /></td>
		</tr>
		<tr>
			<td align="right"><strong>Default SSL port or range</strong></td>
			<td align="left"><input name="net_port_ssl" type="text" id="net_port_ssl" value="{$cfg.net_port_ssl}" size="11" maxlength="11" /></td>
		</tr>
		<tr>
			<td align="right"><strong>Show irc hyperlinks on channels</strong></td>
			<td align="left">yes <input type="radio" name="channel_href_show" value="1"{if $cfg.channel_href_show} checked="checked"{/if} /> <input type="radio" name="channel_href_show" value="0"{if !$cfg.channel_href_show} checked="checked"{/if} /> no</td>
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
			<td align="right"><strong>Block channel info for secret/private channels</strong></td>
			<td align="left">yes <input type="radio" name="block_spchans" value="1"{if $cfg.block_spchans} checked="checked"{/if} /> <input type="radio" name="block_spchans" value="0"{if !$cfg.block_spchans} checked="checked"{/if} /> no</td>
		</tr>
		<tr>
			<td align="right"><strong>Hide Ulined Servers</strong></td>
			<td align="left">yes <input type="radio" name="hide_ulined" value="1"{if $cfg.hide_ulined} checked="checked"{/if} /> <input type="radio" name="hide_ulined" value="0"{if !$cfg.hide_ulined} checked="checked"{/if} /> no</td>
		</tr>
		<tr>
			<td align="right">Servers you don't want MagIRC to show.<br />Separate with commas, example: &quot;hub.mynet.tld,hub2.mynet.tld&quot;</td>
			<td align="left"><input name="hide_servers" type="text" id="hide_servers" value="{$cfg.hide_servers}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right">Channels you don't want MagIRC to show.<br />Separate with commas, example: &quot;#opers,#services&quot;</td>
			<td align="left"><input name="hide_chans" type="text" id="hide_chans" value="{$cfg.hide_chans}" size="32" maxlength="64" /></td>
		</tr>
	</table>
	<button id="network-submit" type="button">Save</button>
</form>

{jsmin}
<script type="text/javascript"><!--{literal}
$(document).ready(function() {
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