<h1>Channel info for {$target}</h1>

<div class="halfleft">

<div id="chan_topic" class="topic"></div>
<div style="text-align:right;">Topic set by <span id="chan_topic_author" class="val"></span> on <span id="chan_topic_time" class="val"></span></div>
<br />
<table class="details">
	<tr><th>Current users:</th><td><span id="chan_users" class="val"></span></td></tr>
	<tr><th>User peak:</th><td><span id="chan_users_max" class="val"></span> on <span id="chan_users_max_time"></span></td></tr>
	<tr><th>Modes:</th><td><span id="chan_modes" class="val"></span></td></tr>
	<tr><th>Kicks:</th><td><span id="chan_kicks" class="val"></span></td></tr>
</table>

</div>

<div class="halfright">
	<h2>Users currently in channel</h2>
	<table id="tbl_users" class="display">
		<thead>
			<tr><th>Nickname</th><th>Status</th><th>Modes</th></tr>
		</thead>
		<tbody>
			<tr><td colspan="3">Loading...</td></tr>
		</tbody>
	</table>
</div>

<div class="clear"></div>

{jsmin}
<script type="text/javascript"><!--
var target = '{$target|escape:'url'}';
{literal}
$(document).ready(function() {
	$.getJSON('rest/denora.php/channels/'+target, function(result) {
		$("#chan_topic").html(result.topic_html);
		$("#chan_topic_author").html(result.topic_author);
		$("#chan_topic_time").html(result.topic_time);
		$("#chan_users").html(result.users);
		$("#chan_users_max").html(result.users_max);
		$("#chan_users_max_time").html(result.users_max_time);
		$("#chan_modes").html("+"+result.modes);
		$("#chan_kicks").html(result.kicks);
	});
	$('#tbl_users').dataTable({
		"iDisplayLength": 10,
		"sPaginationType": "two_button",
		"aaSorting": [[ 0, "asc" ]],
		"sAjaxSource": 'rest/denora.php/channels/'+target+'/users?format=datatables',
		"aoColumns": [
			{ "mDataProp": "nick", "fnRender": function(oObj) { return '<strong>'+oObj.aData['nick']+'<\/strong>'; } },
			{ "mDataProp": "away", "fnRender": function (oObj) {
				var out = oObj.aData['away'] ? '<img src="theme/'+theme+'/img/status/user-away.png" alt="away" title="Away" \/>' : '<img src="theme/'+theme+'/img/status/user-online.png" alt="online" title="Online" \/>';
				if (oObj.aData['bot']) out += '<img src="theme/'+theme+'/img/status/bot.png" alt="bot" title="Bot" \/>';
				if (oObj.aData['helper']) out += '<img src="theme/'+theme+'/img/status/help.png" alt="help" title="Available for help" \/>';
				if (oObj.aData['uline']) out += '<img src="theme/'+theme+'/img/status/service.png" alt="service" title="Service" \/>';
				return out;
			} },
			{ "mDataProp": "modes" }
		]
	});
	$("#tbl_users tbody tr").live("click", function(event) {
		window.location = url_base + 'user/nick:' + encodeURIComponent(this.id) + '/profile';
	});
});
{/literal}
--></script>
{/jsmin}