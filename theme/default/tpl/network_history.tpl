<h1>{t}Network Usage History{/t}</h1>
<div id="chart-history" style="height: 350px; min-width: 700px"></div>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
    var seriesOptions = [],
        seriesCounter = 0,
        names = ['servers', 'channels', 'users'],
		namesLang = { 'servers': mLang.Servers, 'channels': mLang.Channels, 'users': mLang.Users };

    $.each(names, function(i, name) {
        $.getJSON('rest/denora.php/'+ name.toLowerCase() +'/hourlystats', function(data) {
            seriesOptions[i] = { name: namesLang[name], data: data };
            seriesCounter++;
            if (seriesCounter == names.length) {
                createChart();
            }
        });
    });

    function createChart() {
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
            series: seriesOptions
        });
    }

});
{/literal}
</script>
{/jsmin}