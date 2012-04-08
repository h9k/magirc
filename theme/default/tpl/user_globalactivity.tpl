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
				return getUserStatus(oObj.aData) + ' ' + getCountryFlag(oObj.aData) + ' <strong>'+oObj.aData['name']+'</strong>' + getUserExtra(oObj.aData);
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