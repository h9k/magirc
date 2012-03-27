

<h1>Current Country Statistics</h1>
<div id="chart-countries" style="min-width: 700px; height: 400px; margin: 0 auto"></div>

<script type="text/javascript">
<!--
$(document).ready(function() {
    $.getJSON('rest/denora.php/countrystats/global', function(data) {
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
