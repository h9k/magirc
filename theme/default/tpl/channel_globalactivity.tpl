<h1>{t}Global channel activity{/t}</h1>

<form>
	<div id="radio" class="choser">
		<input type="radio" id="radio0" name="radio" /><label for="radio0">{t}Total{/t}</label>
		<input type="radio" id="radio1" name="radio" /><label for="radio1">{t}Today{/t}</label>
		<input type="radio" id="radio2" name="radio" /><label for="radio2">{t}This Week{/t}</label>
		<input type="radio" id="radio3" name="radio" checked="checked" /><label for="radio3">{t}This Month{/t}</label>
	</div>
</form>

<table id="tbl_activity" class="display clickable">
	<thead>
		<tr><th>{t}Channel{/t}</th><th>{t}Letters{/t}</th><th>{t}Words{/t}</th><th>{t}Lines{/t}</th><th>{t}Actions{/t}</th><th>{t}Smileys{/t}</th><th>{t}Kicks{/t}</th><th>{t}Modes{/t}</th><th>{t}Topics{/t}</th></tr>
	</thead>
	<tbody>
		<tr><td colspan="9">{t}Loading{/t}...</td></tr>
	</tbody>
</table>

{jsmin}
<script type="text/javascript">
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
				return getChannelLinks(oObj.aData['name']) + ' ' + oObj.aData['name'];
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
		if (this.id) window.location = url_base + 'channel/' + encodeURIComponent(this.id) + '/profile#activity';
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
</script>
{/jsmin}
