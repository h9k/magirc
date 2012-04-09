<h1>Channel activity for {$target}</h1>

<form>
	<div id="radio" class="choser">
		<input type="radio" id="radio0" name="radio" /><label for="radio0">Total</label>
		<input type="radio" id="radio1" name="radio" /><label for="radio1">Today</label>
		<input type="radio" id="radio2" name="radio" /><label for="radio2">This Week</label>
		<input type="radio" id="radio3" name="radio" checked="checked" /><label for="radio3">This Month</label>
	</div>
</form>

<div id="chart_activity" style="height: 225px;"></div>

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
var target = '{$target|escape:'url'}';
{literal}
$(document).ready(function() {
	var type = 3;
	var chart_activity = new Highcharts.Chart({
		chart: { renderTo: 'chart_activity', type: 'column' },
		xAxis: { type: 'linear', categories: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23 ], title: { text: 'Hour' } },
		yAxis: { min: 0, title: { text: 'Lines' } },
		tooltip: { enabled: false },
		series: [{ name: 'Lines', data: [] }]
	});
	function updateChart() {
		$.getJSON('rest/denora.php/channels/'+target+'/hourly/'+type, function(result) {
			chart_activity.series[0].setData(result);
		});
	}
	var oTable = $('#tbl_activity').dataTable({
		"bServerSide": true,
		"iDisplayLength": 10,
		"aaSorting": [[ 3, "desc" ]],
		"sAjaxSource": "rest/denora.php/channels/"+target+"/activity/"+type+"?format=datatables",
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
	$("#tbl_activity tbody tr").live("click", function(event) {
		window.location = url_base + 'user/stats:' + encodeURIComponent(this.id) + '/profile#activity';
	});
	$("#radio").buttonset();
	$("#radio").change(function(event) {
		type = $('input[name=radio]:checked').index() / 2;
		oTable.fnSettings().sAjaxSource = "rest/denora.php/channels/"+target+"/activity/"+type+"?format=datatables",
		oTable.fnDraw();
		updateChart();
	});
	updateChart();
});
{/literal}
--></script>
{/jsmin}