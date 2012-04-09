<h1>Services integration</h1>
<form id="services-form">
	<table border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td align="right">
				<strong>Google AdSense Client ID</strong>
				<br />If you would like to support MagIRC you can use "pub-2514457845805307" ;)
			</td>
			<td align="left"><input name="service_adsense_id" type="text" id="service_adsense_id" value="{$cfg.service_adsense_id}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right">
				<strong>Google AdSense Channel</strong> (optional)
			</td>
			<td align="left"><input name="service_adsense_channel" type="text" id="service_adsense_channel" value="{$cfg.service_adsense_channel}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right">
				<strong>SearchIRC</strong>
				<br />Set your network ID of SearchIRC features.
				<br />For more information about being ranked on SearchIRC visit <a href="http://searchirc.com/">http://searchirc.com/</a>
				<br />To find out your ID go to your network information page (usually http://searchirc.com/network/YourNetwork)
				<br />then right click on the Users graph on the right to get its path and get the number from the 'n=' parameter.
			</td>
			<td align="left"><input name="service_searchirc" type="text" id="service_" value="{$cfg.service_searchirc}" size="32" maxlength="64" /></td>
		</tr>
		<tr>
			<td align="right">
				<strong>Netsplit</strong>
				<br />The URL parameter for the Netsplit.de features, usually your network name.
				<br />For more information about being ranked on Netsplit.de visit <a href="http://irc.netsplit.de/">http://irc.netsplit.de/</a>
			</td>
			<td align="left"><input name="service_netsplit" type="text" id="service_" value="{$cfg.service_netsplit}" size="32" maxlength="64" /></td>
		</tr>
	</table>
	<button id="services-submit" type="button">Save</button>
</form>

{jsmin}
<script type="text/javascript"><!--{literal}
$(function() {
	$("#services-submit").button().click(function() {
		$("#services-form").ajaxSubmit({ url: 'index.php/configuration', type: 'post', success: function(data) {
			if (data) $("#success").show().delay(1500).fadeOut(500);
			else $("#failure").show().delay(1500).fadeOut(500);
		} });
	});
});
{/literal}
--></script>
{/jsmin}