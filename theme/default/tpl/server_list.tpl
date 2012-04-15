<h1>Server list</h1>
{if $cfg.net_roundrobin}
	Connect to our network round robin <strong>{$cfg.net_roundrobin}</strong>:
	<a href="irc://{$cfg.net_roundrobin}{if $cfg.net_port}:{$cfg.net_port}{/if}"><img src="theme/{$cfg.theme}/img/icons/server-link.png" alt="standard" title="Standard connection" /></a>
	{if $cfg.net_port_ssl} <a href="irc://{$cfg.net_roundrobin}:+{$cfg.net_port_ssl}"><img src="theme/{$cfg.theme}/img/icons/ssl.png" alt="ssl" title="Secure connection" /></a>{/if}
	<br /><br />
{/if}

<table id="tbl_servers" class="display clickable">
<thead>
	<tr>
		<th>Status</th>
		<th>Server</th>
		<th>Description</th>
		<th>Users</th>
		<th>Operators</th>
	</tr>
</thead>
<tbody>
	<tr><td colspan="5">Loading...</td></tr>
</tbody>
</table>
</div>

<div id="dialog-server" title="Server">
	<div class="halfleft">
		<table class="details">
			<tr><th>Description:</th><td><span id="srv_description" class="val"></span></td></tr>
			<tr><th>Online:</th><td><span id="srv_online" class="val"></span></td></tr>
			<tr><th>Version:</th><td><span id="srv_version" class="val"></span></td></tr>
			<tr><th>Uptime:</th><td><span id="srv_uptime" class="val"></span> hours</td></tr>
			{*<tr><th>Connected since:</th><td><span id="srv_connecttime" class="val"></span></td></tr>*}
			<tr><th>Last split:</th><td><span id="srv_lastsplit" class="val"></span></td></tr>
		</table>
	</div>
	<div class="halfright">
		<table class="details">
			<tr><th>Last ping:</th><td><span id="srv_ping" class="val"></span></td></tr>
			<tr><th>Highest ping:</th><td><span id="srv_maxping" class="val"></span> on <span id="srv_maxpingtime"></td></tr>
			<tr><th>Current users:</th><td><span id="srv_users" class="val"></span></td></tr>
			<tr><th>Max users:</th><td><span id="srv_maxusers" class="val"></span> on <span id="srv_maxusertime"></td></tr>
			<tr><th>Current opers:</th><td><span id="srv_opers" class="val"></span></td></tr>
			<tr><th>Max opers:</th><td><span id="srv_maxopers" class="val"></span> on <span id="srv_maxopertime"></td></tr>
		</table>
	</div>
	<div id="srv_motd" class="motd clear" title="MOTD"><pre id="srv_motd_txt"></pre></div>
</div>

{jsmin}
<script type="text/javascript"><!--
{literal}
$(document).ready(function() {
	$('#tbl_servers').dataTable({
		"iDisplayLength": 25,
		"aaSorting": [[ 1, "asc" ]],
		"sAjaxSource": 'rest/denora.php/servers?format=datatables',
		"aoColumns": [
			{ "mDataProp": "online", "fnRender": function (oObj) { return oObj.aData['online'] ? '<img src="theme/'+theme+'/img/status/online.png" alt="online" title="online" \/>' : '<img src="theme/'+theme+'/img/status/offline.png" alt="offline" title="offline" \/>'; } },
			{ "mDataProp": "server", "fnRender": function (oObj) {
				var out = '<a href="irc://'+oObj.aData['server']+':'+net_port+'"><img src="theme/'+theme+'/img/icons/server-link.png" alt="connect" title="Standard connection" /></a>';
				if (net_port_ssl) out += ' <a href="irc://'+oObj.aData['server']+':+'+net_port_ssl+'"><img src="theme/'+theme+'/img/icons/ssl.png" alt="connect" title="Secure connection" /></a>';
				return out + ' ' + oObj.aData['server'];
			} },
			{ "mDataProp": "description" },
			{ "mDataProp": "users" },
			{ "mDataProp": "opers" }
		]
	});
	$("#tbl_servers tbody tr").live("click", function() {
		$.getJSON("rest/denora.php/servers/"+this.id, function(data){
			if (data) {
				$("#dialog-server").dialog("option", "title", data.server);
				$("#srv_description").html(data.description);
				$("#srv_online").html(data.online ? "Yes" : "No");
				$("#srv_version").html(data.version);
				$("#srv_uptime").html(Math.round(data.uptime / 3600));
				//$("#srv_connecttime").html($.format.date(data.connect_time, format_datetime));
				$("#srv_lastsplit").html($.format.date(data.split_time, format_datetime));
				$("#srv_ping").html(data.ping);
				$("#srv_maxping").html(data.ping_max);
				$("#srv_maxpingtime").html($.format.date(data.ping_max_time, format_datetime));
				$("#srv_users").html(data.users);
				$("#srv_maxusers").html(data.users_max);
				$("#srv_maxusertime").html($.format.date(data.users_max_time, format_datetime));
				$("#srv_opers").html(data.opers);
				$("#srv_maxopers").html(data.opers_max);
				$("#srv_maxopertime").html($.format.date(data.opers_max_time, format_datetime));
				$("#srv_motd_txt").html(data.motd ? data.motd_html : "MOTD not available for this server");
				$("#dialog-server").dialog("open");
				$("#srv_motd").scrollTop(0);
			} else {
				alert("Failed");
			}
		}, "json");
	});
	$("#tbl_servers tbody tr a").live("click", function(e) { e.stopPropagation(); });
	// Server dialog
	$("#dialog-server").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 540,
		width: 700,
		modal: true,
		show: 'fade',
		hide: 'fade',
		buttons: {
			"Close": function() {
				$(this).dialog("close");
			}
		}
	});
});
{/literal}
--></script>
{/jsmin}