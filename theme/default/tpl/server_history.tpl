

<h1>Server history</h1>
<div id="chart-history" style="height: 350px; min-width: 700px"></div>

<script type="text/javascript">
<!--
$(document).ready(function() {
    $.getJSON('rest/denora.php/servers/hourlystats', function(data) {
        new Highcharts.StockChart({
            chart: { renderTo: 'chart-history' },
			yAxis: { min: 0 },
            series: [{
                name: 'Servers online',
                data: data,
                step: true,
                tooltip: { valueDecimals: 0 }
            }]
        });
    });
});
-->
</script>
