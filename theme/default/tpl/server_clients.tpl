<h1>{t 1=$target}Current Client Statistics for %1{/t}</h1>
<div id="chart-clients" style="min-width: 700px; height: 400px; margin: 0 auto"></div>

<table id="tbl_clients" class="display">
	<thead>
		<tr><th>{t}Client{/t}</th><th>{t}Count{/t}</th></tr>
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
        table_clients.ajax.reload();
    }
    function loadContent() {
        $.getJSON('rest/service.php/servers/'+target+'/clients/percent', function(data) {
            var colors = Highcharts.getOptions().colors;
            var clientData = data.clients;
            var versionData = data.versions;
            var i = 0, j = 0, name = '';
            $.each(clientData, function(key, value) {
                clientData[key]['color'] = colors[i++];
                if (i > 8) i = 0;
            });
            i = -1;
            $.each(versionData, function(key, value) {
                if (name != value['cat']) {
                    name = value['cat'];
                    i++;
                    j = 0;
                }
                var brightness = 0.2 - (j / 10 / 5);
                versionData[key]['color'] = Highcharts.Color(colors[i]).brighten(brightness).get();
                if (i > 8) i = 0;
                j++;
            });
            pie_clients.series[0].setData(clientData);
            pie_clients.series[1].setData(versionData);
        });
    }
    var pie_clients = new Highcharts.Chart({
        chart: { renderTo: 'chart-clients', type: 'pie' },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.point.name + (this.point.version ? ' ' + this.point.version : '') +'<\/b>: '+ this.y +'%'+' ('+this.point.count+')';
            }
        },
        series: [{
            name: 'Clients',
            data: [],
            size: '60%',
            dataLabels: {
                formatter: function() {
                    return this.y > 5 ? this.point.name.substring(0, 12) : null;
                },
                color: 'white',
                distance: -30
            }
        }, {
            name: 'Versions',
            data: [],
            innerSize: '60%',
            dataLabels: {
                formatter: function() {
                    var client = this.point.name.substring(0, 12) + (this.point.version ? ' ' + this.point.version : '');
                    return this.y > 1 ? '<b>' + client.substring(0, 30) + ':</b> '+ this.y +'%'+' ('+this.point.count+')' : null;
                }
            }
        }]
    });
	var table_clients = $('#tbl_clients').DataTable({
		"pageLength": 10,
		"order": [[ 1, "desc" ]],
		"ajax": "rest/service.php/servers/"+target+"/clients?format=datatables",
		"columns": [
			{ "data": "client", "render": function (data) {
				return data ? data : mLang.Unknown;
			} },
			{ "data": "count" }
		]
	});
    loadContent();
});
{/literal}
</script>
{/jsmin}