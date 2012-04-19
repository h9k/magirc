<h1>Current Country Statistics</h1>
<div id="chart-countries" style="min-width: 700px; height: 400px; margin: 0 auto"></div>

<table id="tbl_countries" class="display">
	<thead>
		<tr><th>Country</th><th>Count</th></tr>
	</thead>
	<tbody>
		<tr><td colspan="2">{t}Loading{/t}...</td></tr>
	</tbody>
</table>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
    $.getJSON('rest/denora.php/countries/percent', function(data) {
        new Highcharts.Chart({
			chart: { renderTo: 'chart-countries' },
			tooltip: {
				formatter: function() {
					return '<b>'+ this.point.name +'<\/b>: '+ Math.round(this.percentage * 100) / 100 +' %';
				}
			},
			series: [{ type: 'pie', name: mLang.CountryStatistics, data: data }]
		});
	});
	$('#tbl_countries').dataTable({
		"iDisplayLength": 10,
		"aaSorting": [[ 1, "desc" ]],
		"sAjaxSource": "rest/denora.php/countries?format=datatables",
		"aoColumns": [
			{ "mDataProp": "country", "fnRender": function(oObj) {
				return getCountryFlag(oObj.aData) + ' ' + oObj.aData['country'];
			} },
			{ "mDataProp": "count" }
		]
	});
});
{/literal}
</script>
{/jsmin}