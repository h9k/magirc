<h1>Current Country Statistics for {$smarty.get.chan}</h1>
<div id="chart-countries" style="min-width: 700px; height: 400px; margin: 0 auto"></div>

<script type="text/javascript">
<!--
$(document).ready(function() {
    $.getJSON('rest/denora.php/countrystats/{$smarty.get.chan|escape:'url'}', function(data) {
		new Highcharts.Chart({
			chart: { renderTo: 'chart-countries' },
			tooltip: {
				formatter: function() {
					return '<b>'+ this.point.name +'<\/b>: '+ Math.round(this.percentage * 100) / 100 +' %';
				}
			},
			series: [{ type: 'pie', name: 'Country Statistics', data: data }]
		});
	});
});
-->
</script>
