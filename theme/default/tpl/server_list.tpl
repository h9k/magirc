<h1>Server list</h1>
<table id="tbl_servers" class="display">
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
			<tr><th>Connected since:</th><td><span id="srv_connecttime" class="val"></span></td></tr>
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
			{ "mDataProp": "online", "fnRender": function (oObj) { return oObj.aData['online'] ? '<img src="theme/default/img/status/online.png" alt="online" title="online" \/>' : '<img src="theme/default/img/status/offline.png" alt="offline" title="offline" \/>'; } },
			{ "mDataProp": "server", "fnRender": function (oObj) { return "<strong>" + oObj.aData['server'] + "<\/strong>"; } },
			{ "mDataProp": "comment" },
			{ "mDataProp": "currentusers" },
			{ "mDataProp": "opers" }
		]
	});
	$("#tbl_servers tbody tr").live("click", function() {
		$.getJSON("rest/denora.php/servers/"+this.id, function(data){
			if (data) {
				$("#dialog-server").dialog("option", "title", data.server);
				$("#srv_description").html(data.comment);
				$("#srv_online").html(data.online == 'Y' ? "Yes" : "No");
				$("#srv_version").html(data.version);
				$("#srv_uptime").html(Math.round(data.uptime / 3600));
				$("#srv_connecttime").html(data.connecttime);
				$("#srv_lastsplit").html(data.lastsplit);
				$("#srv_ping").html(data.ping);
				$("#srv_maxping").html(data.highestping);
				$("#srv_maxpingtime").html(data.maxpingtime);
				$("#srv_users").html(data.currentusers);
				$("#srv_maxusers").html(data.maxusers);
				$("#srv_maxusertime").html(data.maxusertime);
				$("#srv_opers").html(data.opers);
				$("#srv_maxopers").html(data.maxopers);
				$("#srv_maxopertime").html(data.maxopertime);
				$("#srv_motd_txt").html(data.motd ? data.motd_html : "MOTD not available for this server");
				$("#dialog-server").dialog("open");
				$("#srv_motd").scrollTop(0);
			} else {
				alert("Failed");
			}
		}, "json");
	});
	// Server dialog
	$("#dialog-server").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 540,
		width: 700,
		modal: true,
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