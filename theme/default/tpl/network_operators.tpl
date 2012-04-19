<h1>{t}Operators currently online{/t}</h1>
<table id="tbl_operators" class="display clickable">
<thead>
	<tr>
		<th>{t}Nickname{/t}</th>
		<th>{t}Server{/t}</th>
		<th>{t}Online since{/t}</th>
	</tr>
</thead>
<tbody>
	<tr><td colspan="3">{t}Loading{/t}...</td></tr>
</tbody>
</table>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#tbl_operators').dataTable({
		"iDisplayLength": 25,
		"aaSorting": [[ 0, "asc" ]],
		"sAjaxSource": 'rest/denora.php/operators?format=datatables',
		"aoColumns": [
			{ "mDataProp": "nickname", "fnRender": function(oObj) {
				return getUserStatus(oObj.aData) + ' ' + getCountryFlag(oObj.aData) + ' ' + oObj.aData['nickname'] + getUserExtra(oObj.aData);
			} },
			{ "mDataProp": "server" },
			{ "mDataProp": "connect_time", "fnRender": function(oObj) { return $.format.date(oObj.aData['connect_time'], format_datetime); } }
		]
	});
	$("#tbl_operators tbody tr").live("click", function() {
		if (this.id) window.location = url_base + 'user/nick:' + encodeURIComponent(this.id) + '/profile';
	});
});
{/literal}
</script>
{/jsmin}
