<h1>{t}Global channel activity{/t}</h1>

<form>
	<div id="radio" class="choser">
		<input type="radio" id="radio0" name="radio" value="total" /><label for="radio0">{t}Total{/t}</label>
		<input type="radio" id="radio1" name="radio" value="daily" /><label for="radio1">{t}Today{/t}</label>
		<input type="radio" id="radio2" name="radio" value="weekly" /><label for="radio2">{t}This Week{/t}</label>
		<input type="radio" id="radio3" name="radio" value="monthly" checked="checked" /><label for="radio3">{t}This Month{/t}</label>
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
	var type = 'monthly';
	var table = $('#tbl_activity').DataTable({
		"serverSide": true,
		"pageLength": 25,
		"order": [[ 3, "desc" ]],
		"ajax": "rest/service.php/channels/activity/"+type+"?format=datatables",
		"columns": [
			{ "data": "name", "render": function (data) {
				return getChannelLinks(data) + ' ' + data;
			} },
			{ "data": "letters" },
			{ "data": "words" },
			{ "data": "lines" },
			{ "data": "actions" },
			{ "data": "smileys" },
			{ "data": "kicks" },
			{ "data": "modes" },
			{ "data": "topics" }
		]
	});
	$("#tbl_activity tbody").on("click", "tr", function(event) {
		if (this.id) window.location = url_base + 'channel/' + encodeURIComponent(this.id) + '/profile#activity';
	});
	$("#tbl_activity tbody").on("click", "tr button", function(e) {
		e.stopPropagation();
		openChanMenu(this);
	});
	$("#radio").buttonset();
	$("#radio").change(function(event) {
		type = $('input[name=radio]:checked').val();
		table.fnSettings().ajax = "rest/service.php/channels/activity/"+type+"?format=datatables";
		table.fnDraw();
	});
});
{/literal}
</script>
{/jsmin}
