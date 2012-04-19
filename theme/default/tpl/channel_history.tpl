<h1>{t}Channel history{/t}</h1>
<div id="chart-history" style="height: 350px; min-width: 700px"></div>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
    $.getJSON('rest/denora.php/channels/hourlystats', function(data) {
        new Highcharts.StockChart({
            chart: { renderTo: 'chart-history' },
			yAxis: { min: 0 },
			rangeSelector: {
				buttons: [{
					type: 'day',
					count: 1,
					text: mLang.zoom_1d
				},{
					type: 'week',
					count: 1,
					text: mLang.zoom_1w
				},{
					type: 'month',
					count: 1,
					text: mLang.zoom_1m
				}, {
					type: 'month',
					count: 3,
					text: mLang.zoom_3m
				}, {
					type: 'month',
					count: 6,
					text: mLang.zoom_6m
				}, {
					type: 'year',
					count: 1,
					text: mLang.zoom_1y
				}, {
					type: 'all',
					text: mLang.zoom_All
				}],
				selected: 3
			},
            series: [{
                name: mLang.Channels,
                data: data,
                step: false,
                tooltip: { valueDecimals: 0 }
            }]
        });
    });
});
{/literal}
</script>
{/jsmin}