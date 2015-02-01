<h1>{t 1=$target}Current Country Statistics for %1{/t}</h1>
<div id="chart-countries" style="min-width: 700px; height: 400px; margin: 0 auto"></div>

<table id="tbl_countries" class="display">
	<thead>
		<tr><th>{t}Country{/t}</th><th>{t}Count{/t}</th></tr>
	</thead>
	<tbody>
		<tr><td colspan="2">{t}Loading{/t}...</td></tr>
	</tbody>
</table>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
    if (refresh_interval > 0) {
        setInterval(updateContent, refresh_interval);
    }
    function updateContent() {
        loadContent();
        table_countries.ajax.reload(null, false);
    }
    function loadContent() {
        $.getJSON('rest/service.php/channels/'+target+'/countries/percent', function(data) {
            pie_countries.series[0].setData(data);
        });
    }
    var pie_countries = new Highcharts.Chart({
        chart: { renderTo: 'chart-countries' },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.point.name +'<\/b>: '+ this.y +' %'+' (' + this.point.count +')';
            }
        },
        series: [{
            type: 'pie',
            name: mLang.CountryStatistics,
            data: [],
            dataLabels: {
                formatter: function() {
                    return '<b>'+ this.point.name +'<\/b>: '+ this.y +' %'+' (' + this.point.count +')';
                }
            }
        }]
    });
	var table_countries = $('#tbl_countries').DataTable({
		"pageLength": 10,
		"order": [[ 1, "desc" ]],
		"ajax": "rest/service.php/channels/"+target+"/countries?format=datatables",
		"columns": [
			{ "data": "country", "render": function(data, type, row) {
				return getCountryFlag(row) + ' ' + data;
			} },
			{ "data": "count" }
		]
	});
    loadContent();
});
{/literal}
</script>
{/jsmin}