<h1>{t}Current Client Statistics{/t}</h1>
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
    $.getJSON('rest/denora.php/network/clients/percent', function(data) {
        new Highcharts.Chart({
			chart: { renderTo: 'chart-clients', type: 'pie' },
			tooltip: {
				formatter: function() {
					return '<b>'+ this.point.name +'<\/b>: '+ this.y +'%';
				}
			},
			series: [{
				name: 'Clients',
				data: data.clients,
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
				data: data.versions,
				innerSize: '60%',
				dataLabels: {
					formatter: function() {
						return this.y > 1 ? '<b>'+ this.point.name +':</b> '+ this.y +'%' : null;
					}
				}
			}]
		});
	});
	$('#tbl_clients').dataTable({
		"iDisplayLength": 10,
		"aaSorting": [[ 1, "desc" ]],
		"sAjaxSource": "rest/denora.php/network/clients?format=datatables",
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