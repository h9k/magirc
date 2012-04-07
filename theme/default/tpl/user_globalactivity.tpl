<h1>Global user activity</h1>

<form>
	<div id="radio" class="choser">
		<input type="radio" id="radio0" name="radio" /><label for="radio0">Total</label>
		<input type="radio" id="radio1" name="radio" /><label for="radio1">Today</label>
		<input type="radio" id="radio2" name="radio" /><label for="radio2">This Week</label>
		<input type="radio" id="radio3" name="radio" checked="checked" /><label for="radio3">This Month</label>
	</div>
</form>

<table id="tbl_activity" class="display">
	<thead>
		<tr><th>Nickname</th><th>Letters</th><th>Words</th><th>Lines</th><th>Actions</th><th>Smileys</th><th>Kicks</th><th>Modes</th><th>Topics</th></tr>
	</thead>
	<tbody>
		<tr><td colspan="9">Loading...</td></tr>
	</tbody>
</table>

{jsmin}
<script type="text/javascript"><!--
{literal}
$(document).ready(function() {
	var type = 3;
	var oTable = $('#tbl_activity').dataTable({
		"bServerSide": true,
		"iDisplayLength": 25,
		"aaSorting": [[ 3, "desc" ]],
		"sAjaxSource": "rest/denora.php/users/activity/"+type+"?format=datatables",
		"aoColumns": [
			{ "mDataProp": "name", "fnRender": function(oObj) {
				var aData = oObj.aData;
				var out = "";
				if (aData['away']) out = '<img src="theme/'+theme+'/img/status/user-away.png" alt="away" title="Away as '+aData['nick']+'" \/>';
				else if (aData['online']) out = '<img src="theme/'+theme+'/img/status/user-online.png" alt="online" title="Online as '+aData['nick']+'" \/>';
				else out = '<img src="theme/'+theme+'/img/status/user-offline.png" alt="offline" title="Offline" \/>';
				if (aData['country_code'] != '' && aData['country_code'] != '??' && aData['country_code'] != 'local') {
					out += ' <img src="theme/'+theme+'/img/flags/'+aData['country_code']+'.png" alt="'+aData['country_code']+'" title="'+aData['country']+'" />';
				} else {
					out += ' <img src="theme/'+theme+'/img/flags/unknown.png" alt="Unknown" title="Unknown" />';
				}
				out += ' <strong>'+aData['name']+'</strong>';
				if (aData['bot']) out += ' <img src="theme/'+theme+'/img/status/bot.png" alt="bot" title="Bot" \/>';
				if (aData['service']) out += ' <img src="theme/'+theme+'/img/status/service.png" alt="service" title="Service" \/>';
				if (aData['operator']) out += ' <img src="theme/'+theme+'/img/status/operator.png" alt="oper" title="Operator" \/>';
				if (aData['helper']) out += ' <img src="theme/'+theme+'/img/status/help.png" alt="help" title="Available for help" \/>';
				return out;
			} },
			{ "mDataProp": "letters" },
			{ "mDataProp": "words" },
			{ "mDataProp": "lines" },
			{ "mDataProp": "actions" },
			{ "mDataProp": "smileys" },
			{ "mDataProp": "kicks" },
			{ "mDataProp": "modes" },
			{ "mDataProp": "topics" }
		]
	});
	$("#tbl_activity tbody tr").live("click", function() {
		window.location = url_base + 'user/stats:' + encodeURIComponent(this.id) + '/profile#activity';
	});
	$("#radio").buttonset();
	$("#radio").change(function(event) {
		type = $('input[name=radio]:checked').index() / 2;
		oTable.fnSettings().sAjaxSource = "rest/denora.php/users/activity/"+type+"?format=datatables";
		oTable.fnDraw();
	});
});
{/literal}
--></script>
{/jsmin}