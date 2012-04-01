<h1>User activity for {$target}</h1>

<form>
	<div id="radio" class="choser">
		<input type="radio" id="radio0" name="radio" checked="checked" value="global" /><label for="radio0">Global</label>
	</div>
</form>

<div id="chart_activity" style="height: 225px;"></div>

<form>
	<div id="type" class="choser">
		<input type="radio" id="type0" name="type" /><label for="type0">Total</label>
		<input type="radio" id="type1" name="type" /><label for="type1">Today</label>
		<input type="radio" id="type2" name="type" /><label for="type2">This Week</label>
		<input type="radio" id="type3" name="type" checked="checked" /><label for="type3">This Month</label>
	</div>
</form>

<table id="tbl_activity" class="display">
	<thead>
		<tr>
			<th>Type</th>
			<th>Letters</th>
			<th>Words</th>
			<th>Lines</th>
			<th>Actions</th>
			<th>Smileys</th>
			<th>Kicks</th>
			<th>Modes</th>
			<th>Topics</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="9">Loading...</td>
		</tr>
	</tbody>
</table>

{jsmin}
<script type="text/javascript"><!--
var target = '{$target|escape:'url'}';
var mode = '{$mode}';
{literal}
$(document).ready(function() {
	var chan = 'global';
	var type = 3;
	var chart_activity = new Highcharts.Chart({
		chart: { renderTo: 'chart_activity', type: 'column' },
		xAxis: { type: 'linear', categories: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23 ], title: { text: 'Hour' } },
		yAxis: { min: 0, title: { text: 'Lines' } },
		tooltip: { enabled: false },
		series: [{ name: 'Lines', data: [] }]
	});
	function updateChart() {
		$.getJSON('rest/denora.php/users/'+mode+'/'+target+'/hourly/'+encodeURIComponent(chan)+'/'+type, function(result) {
			chart_activity.series[0].setData(result);
		});
	}
	oTable = $("#tbl_activity").dataTable({
		"bFilter": false,
		"bInfo": false,
		"bLengthChange": false,
		"bPaginate": false,
		"bSort": false,
		"bEscapeRegex": false,
		"sAjaxSource": "rest/denora.php/users/"+mode+"/"+target+"/activity/"+encodeURIComponent(chan)+'?format=datatables',
		"aoColumns": [
			{ "mDataProp": "type", "fnRender": function (oObj) {
				switch (oObj.aData['type']) {
					case 0: return 'Total';
					case 1: return 'Today';
					case 2: return 'This Week';
					case 3: return 'This Month';
				}
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
	$("#radio,#type").buttonset();
	$("#radio").change(function(event) {
		chan = $('input[name=radio]:checked').val();
		oTable.fnSettings().sAjaxSource = "rest/denora.php/users/"+mode+"/"+target+"/activity/"+encodeURIComponent(chan)+'?format=datatables';
		oTable.fnReloadAjax();
		updateChart();
	});
	$("#type").change(function(event) {
		type = $('input[name=type]:checked').index() / 2;
		updateChart();
	});
	$.getJSON("rest/denora.php/users/"+mode+"/"+target+"/channels", function(result) {
		var i = 1;
		$.each(result, function(key, value) {
			$("#radio").append('<input type="radio" id="radio'+i+'" name="radio" value="'+value+'"\/><label for="radio'+i+'">'+value+'<\/label>');
			i++;
		});
		$("#radio").buttonset('refresh');
	});
	updateChart();
});
{/literal}
--></script>
{/jsmin}