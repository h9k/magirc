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
    $.getJSON('rest/denora.php/servers/'+target+'/clients/percent', function(data) {
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
        new Highcharts.Chart({
			chart: { renderTo: 'chart-clients', type: 'pie' },
			tooltip: {
				formatter: function() {
					return '<b>'+ this.point.name + (this.point.version ? ' ' + this.point.version : '') +'<\/b>: '+ this.y +'%'+' ('+this.point.count+')';
				}
			},
			series: [{
				name: 'Clients',
				data: clientData,
				size: '60%',
				dataLabels: {
					formatter: function() {
						return this.y > 5 ? this.point.name : null;
					},
					color: 'white',
					distance: -30
				}
			}, {
				name: 'Versions',
				data: versionData,
				innerSize: '60%',
				dataLabels: {
					formatter: function() {
						return this.y > 1 ? '<b>'+ this.point.name + (this.point.version ? ' ' + this.point.version : '') + ':</b> '+ this.y +'%'+' ('+this.point.count+')' : null;
					}
				}
			}]
		});
	});
	$('#tbl_clients').dataTable({
		"iDisplayLength": 10,
		"aaSorting": [[ 1, "desc" ]],
		"sAjaxSource": "rest/denora.php/servers/"+target+"/clients?format=datatables",
		"aoColumns": [
			{ "mDataProp": "client", "fnRender": function (oObj) {
				return oObj.aData['client'] ? oObj.aData['client'] : mLang.Unknown;
			} },
			{ "mDataProp": "count" }
		]
	});
});
{/literal}
</script>
{/jsmin}