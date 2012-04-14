<h1>Global channel activity</h1>

<form>
	<div id="radio" class="choser">
		<input type="radio" id="radio0" name="radio" /><label for="radio0">Total</label>
		<input type="radio" id="radio1" name="radio" /><label for="radio1">Today</label>
		<input type="radio" id="radio2" name="radio" /><label for="radio2">This Week</label>
		<input type="radio" id="radio3" name="radio" checked="checked" /><label for="radio3">This Month</label>
	</div>
</form>

<table id="tbl_activity" class="display clickable">
	<thead>
		<tr><th>Channel</th><th>Letters</th><th>Words</th><th>Lines</th><th>Actions</th><th>Smileys</th><th>Kicks</th><th>Modes</th><th>Topics</th></tr>
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
		"sAjaxSource": "rest/denora.php/channels/activity/"+type+"?format=datatables",
		"aoColumns": [
			{ "mDataProp": "name", "fnRender": function (oObj) {
				var chan = oObj.aData['name'];
				var out = '';
				if (net_roundrobin) out += '<a href="irc://'+net_roundrobin+':'+net_port+'/'+encodeURIComponent(chan)+'"><img src="theme/'+theme+'/img/icons/link.png" alt="connect" title="Standard connection" /></a>';
				if (net_roundrobin && net_port_ssl) out += ' <a href="irc://'+net_roundrobin+':+'+net_port_ssl+'/'+encodeURIComponent(chan)+'"><img src="theme/'+theme+'/img/icons/ssl.png" alt="connect" title="Secure connection" /></a>';
				return out + ' ' + chan;
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
	$("#tbl_activity tbody tr").live("click", function(event) {
		window.location = url_base + 'channel/' + encodeURIComponent(this.id) + '/profile#activity';
	});
	$("#tbl_activity tbody tr a").live("click", function(e) { e.stopPropagation(); });
	$("#radio").buttonset();
	$("#radio").change(function(event) {
		type = $('input[name=radio]:checked').index() / 2;
		oTable.fnSettings().sAjaxSource = "rest/denora.php/channels/activity/"+type+"?format=datatables";
		oTable.fnDraw();
	});
});
{/literal}
--></script>
{/jsmin}