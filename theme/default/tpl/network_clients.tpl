<h1>Current Client Statistics</h1>
<div id="chart-clients" style="min-width: 700px; height: 400px; margin: 0 auto"></div>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
    $.getJSON('rest/denora.php/clientstats', function(data) {
        new Highcharts.Chart({
			chart: { renderTo: 'chart-clients' },
			tooltip: {
				formatter: function() {
					return '<b>'+ this.point.name +'<\/b>: '+ Math.round(this.percentage * 100) / 100 +' %';
				}
			},
			series: [{ type: 'pie', name: 'Client Statistics', data: data }]
		});
	});
});
{/literal}
</script>
{/jsmin}