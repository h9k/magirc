<h1>User info for {$target}</h1>

<table id="tbl_details" class="details">
	<tr><th>Nickname:</th><td><span id="user_nick" class="val"></span></td></tr>
	<tr><th>Aliases:</th><td><span id="user_aliases" class="val"></span></td></tr>
	<tr><th>Stats Username:</th><td><span id="user_uname" class="val"></span></td></tr>
	<tr><th>Real name:</th><td><span id="user_realname" class="val"></span></td></tr>
	<tr><th>Hostname:</th><td><span id="user_hostname" class="val"></span></td></tr>
	<tr><th>Server:</th><td><span id="user_server" class="val"></span></td></tr>
	<tr><th>Country:</th><td><span id="user_country" class="val"></span></td></tr>
	<tr><th>Client:</th><td><span id="user_client" class="val"></span></td></tr>
	<tr><th>Status:</th><td><span id="user_status" class="val"></span><span id="user_status_extra"></span></td></tr>
</table>
<div id="nodata" style="display:none;">Information for this user currently unavailable</div>

{jsmin}
<script type="text/javascript"><!--
var target = '{$target|escape:'url'}';
var mode = '{$mode}';
{literal}
$(document).ready(function() {
    $.getJSON('rest/denora.php/users/'+mode+'/'+target, function(result) {
		if (result) {
			var aliases = '', status = '', status_extra = '';
			$("#user_nick").html(result.nickname);
			$.each(result.aliases, function(key, value) {
				aliases += value + '<br \/>';
			});
			$("#user_aliases").html(aliases ? aliases : '-');
			$("#user_uname").html(result.uname);
			$("#user_realname").html(result.realname);
			status = getUserStatus(result);
			if (result.online) {
				if (result.away) {
					//status += ' Away';
					if (result.away_msg) status_extra += '<br \/>Message: ' + result.away_msg;
				} else {
					//status += ' Online';
				}
				status_extra += '<br \/>Connected since ' + $.format.date(result.connect_time, format_datetime);
			} else {
				//status += ' Offline';
				if (result.lastquit_time) status_extra += '<br \/>Last quit ' + $.format.date(result.quit_time, format_datetime);
				if (result.lastquit_msg) status_extra += '<br \/>Message: ' + result.quit_msg;
			}
			status += getUserExtra(result);
			$("#user_status").html(status);
			$("#user_status_extra").html(status_extra);
			$("#user_hostname").html(result.hostname);
			$("#user_server").html(result.server);
			$("#user_country").html(getCountryFlag(result)+' '+result.country);
			$("#user_client").html(result.client);
		} else {
			$("#tbl_details").hide();
			$("#nodata").show();
		}
	});
});
{/literal}
--></script>
{/jsmin}