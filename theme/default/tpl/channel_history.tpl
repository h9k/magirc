<h1>Channel history</h1>
<div id="chart-history" style="height: 350px; min-width: 700px"></div>

{jsmin}
<script type="text/javascript"><!--
{literal}
$(document).ready(function() {
    $.getJSON('rest/denora.php/channels/hourlystats', function(data) {
        new Highcharts.StockChart({
            chart: { renderTo: 'chart-history' },
			yAxis: { min: 0 },
            series: [{
                name: 'Channels online',
                data: data,
                step: false,
                tooltip: { valueDecimals: 0 }
            }]
        });
    });
});
{/literal}
--></script>
{/jsmin}