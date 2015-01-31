<h1>Services integration</h1>
<form id="services-form">
	<table border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td align="right">
				<strong>Webchat URL</strong>
				<br />The URL-encoded channel name gets appended
				<br />Example: http://webchat.mynet.tld/?chan=
			</td>
			<td align="left"><input name="service_webchat" type="text" id="service_webchat" value="{$cfg->service_webchat}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right">
				<strong>Mibbit settings key</strong>
				<br />This enables links to the Mibbit web chat
			</td>
			<td align="left"><input name="service_mibbit" type="text" id="service_mibbit" value="{$cfg->service_mibbit}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right">
				<strong>Mibbit ID (for stats)</strong>
				<br />Set your network ID of Mibbit features.
				<br />For more information about being indexed by Mibbit visit <a href="http://mibbit.com/">http://mibbit.com/</a>
				<br />To find out your ID go to your network information page<br />(usually http://search.mibbit.com/networks/YourNetwork)
				<br />then right click on the graph on the left to get its path<br />and get the number from the png's name : 1234_week.png
			</td>
			<td align="left"><input name="service_mibbitid" type="text" id="service_mibbitid" value="{$cfg->service_mibbitid}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right">
				<strong>Netsplit ID</strong>
				<br />The URL parameter for the Netsplit.de features, usually your network name.
				<br />For more information about being ranked on Netsplit.de visit <a href="http://irc.netsplit.de/">http://irc.netsplit.de/</a>
			</td>
			<td align="left"><input name="service_netsplit" type="text" id="service_netsplit" value="{$cfg->service_netsplit}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right"><strong>Enable AddThis sharing buttons</strong></td>
			<td align="left">yes <input type="radio" name="service_addthis" value="1"{if $cfg->service_addthis} checked="checked"{/if} /> <input type="radio" name="service_addthis" value="0"{if !$cfg->service_addthis} checked="checked"{/if} /> no</td>
		</tr>
		<tr>
			<td align="right">
				<strong>Google AdSense Client ID</strong>
			</td>
			<td align="left"><input name="service_adsense_id" type="text" id="service_adsense_id" value="{$cfg->service_adsense_id}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right">
				<strong>Google AdSense Channel</strong> (optional)
			</td>
			<td align="left"><input name="service_adsense_channel" type="text" id="service_adsense_channel" value="{$cfg->service_adsense_channel}" size="32" maxlength="64" /></td>
		</tr>
	</table>
	<button id="services-submit" type="button">Save</button>
</form>

{jsmin}
<script type="text/javascript">{literal}
$(document).ready(function() {
	$("#services-submit").button().click(function() {
		$("#services-form").ajaxSubmit({ url: 'index.php/configuration', type: 'post', success: function(data) {
			if (data) $("#success").show().delay(1500).fadeOut(500);
			else $("#failure").show().delay(1500).fadeOut(500);
		} });
	});
});
{/literal}
</script>
{/jsmin}