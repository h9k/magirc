<h1>Network Usage History</h1>
<div id="chart-history" style="height: 350px; min-width: 700px"></div>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
    var seriesOptions = [],
        seriesCounter = 0,
        names = ['Servers', 'Channels', 'Users'];

    $.each(names, function(i, name) {
        $.getJSON('rest/denora.php/'+ name.toLowerCase() +'/hourlystats', function(data) {
            seriesOptions[i] = { name: name, data: data };
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
					text: '1d'
				},{
					type: 'week',
					count: 1,
					text: '1w'
				},{
					type: 'month',
					count: 1,
					text: '1m'
				}, {
					type: 'month',
					count: 3,
					text: '3m'
				}, {
					type: 'month',
					count: 6,
					text: '6m'
				}, {
					type: 'ytd',
					text: 'YTD'
				}, {
					type: 'year',
					count: 1,
					text: '1y'
				}, {
					type: 'all',
					text: 'All'
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