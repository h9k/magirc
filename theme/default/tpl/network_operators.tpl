

<h1>Operators currently online</h1>
<table id="tbl_operators" class="display">
<thead>
	<tr>
		<th>Nickname</th>
		<th>Status</th>
		<th>Server</th>
		<th>Online since</th>
	</tr>
</thead>
<tbody>
	<tr><td colspan="4">Loading...</td></tr>
</tbody>
</table>

<script type="text/javascript">
<!--
$(document).ready(function() {
	$('#tbl_operators').dataTable({
		"iDisplayLength": 25,
		"aaSorting": [[ 0, "asc" ]],
		"sAjaxSource": 'rest/denora.php/operators?format=datatables',
		"aoColumns": [
			{ "mDataProp": "nick", "fnRender": function(oObj) { return '<strong>'+oObj.aData['nick']+'</strong>'; } },
			{ "mDataProp": "away", "fnRender": function (oObj) {
				var out = oObj.aData['away'] ? '<img src="theme/default/img/status/away.png" alt="away" title="Away" \/>' : '<img src="theme/default/img/status/online.png" alt="online" title="Online" \/>';
				if (oObj.aData['bot']) out += '<img src="theme/default/img/status/bot.png" alt="bot" title="Bot" \/>';
				if (oObj.aData['helper']) out += '<img src="theme/default/img/status/help.png" alt="help" title="Available for help" \/>';
				if (oObj.aData['uline']) out += '<img src="theme/default/img/status/service.png" alt="service" title="Service" \/>';
				return out;
			} },
			{ "mDataProp": "server" },
			{ "mDataProp": "connecttime" }
		]
	});
	$("#tbl_operators tbody tr").live("click", function() {
		window.location = url_base + '?section=user&action=profile&nick=' + escape(this.id);
	});
});
-->
</script>