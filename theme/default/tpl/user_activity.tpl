<h1>{t 1=$target}User activity for %1{/t}</h1>

<form>
	<div id="radio" class="choser">
		<input type="radio" id="radio0" name="radio" checked="checked" value="global" /><label for="radio0">{t}Global{/t}</label>
	</div>
</form>

<div id="chart_activity" style="height: 225px;"></div>

<form>
	<div id="type" class="choser">
		<input type="radio" id="type0" name="type" /><label for="type0">{t}Total{/t}</label>
		<input type="radio" id="type1" name="type" /><label for="type1">{t}Today{/t}</label>
		<input type="radio" id="type2" name="type" /><label for="type2">{t}This Week{/t}</label>
		<input type="radio" id="type3" name="type" checked="checked" /><label for="type3">{t}This Month{/t}</label>
	</div>
</form>

<table id="tbl_activity" class="display">
	<thead>
		<tr>
			<th>{t}Type{/t}</th>
			<th>{t}Letters{/t}</th>
			<th>{t}Words{/t}</th>
			<th>{t}Lines{/t}</th>
			<th>{t}Actions{/t}</th>
			<th>{t}Smileys{/t}</th>
			<th>{t}Kicks{/t}</th>
			<th>{t}Modes{/t}</th>
			<th>{t}Topics{/t}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="9">{t}Loading{/t}...</td>
		</tr>
	</tbody>
</table>

{jsmin}
<script type="text/javascript">
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
					case 0: return mLang.Total;
					case 1: return mLang.Today;
					case 2: return mLang.ThisWeek;
					case 3: return mLang.ThisMonth;
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
</script>
{/jsmin}