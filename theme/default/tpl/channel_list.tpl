<h1>Channel list</h1>
<table id="tbl_channels" class="display clickable">
	<thead>
		<tr>
			<th>Channel</th>
			<th>Current users</th>
			<th>Max users</th>
		</tr>
	</thead>
	<tbody>
		<tr><td colspan="3">Loading...</td></tr>
	</tbody>
</table>

<ul id="chanmenu" style="display:none;">
	{if $cfg.net_roundrobin}<li data-action="irc"><a href="#">irc standard connection</a></li>{/if}
	{if $cfg.net_roundrobin && $cfg.net_port_ssl}<li data-action="ircs"><a href="#">irc secure connection</a></li>{/if}
	{if $cfg.service_webchat}<li data-action="webchat"><a href="#">webchat</a></li>{/if}
	{if $cfg.net_roundrobin && $cfg.service_mibbit}<li data-action="mibbit"><a href="#">mibbit</a></li>{/if}
</ul>

{jsmin}
<script type="text/javascript">
var service_webchat = '{$cfg.service_webchat}';
var service_mibbit = '{$cfg.service_mibbit}';
{literal}
$(document).ready(function() {
	$('#tbl_channels').dataTable({
		"bServerSide": true,
		"iDisplayLength": 25,
		"aaSorting": [[ 1, "desc" ]],
		"sAjaxSource": "rest/denora.php/channels?format=datatables",
		"aoColumns": [
			{ "mDataProp": "channel", "fnRender": function (oObj) {
				//return getChannelLinks(oObj.aData['channel']) + ' ' + oObj.aData['channel'];
				if (net_roundrobin || service_webchat) {
					return '<button type="button" title="join..." class="chanbutton ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icons" style="height:20px; width:30px; margin:0; vertical-align:middle;"><span class="ui-button-icon-secondary ui-icon ui-icon-triangle-1-s"></span></button>' + ' ' + oObj.aData['channel'];
				} else {
					oObj.aData['channel'];
				}
			} },
			{ "mDataProp": "users" },
			{ "mDataProp": "users_max" }
		]
	});
	$("#tbl_channels tbody tr").live("click", function(event) {
		window.location = url_base + 'channel/' + encodeURIComponent(this.id) + '/profile';
	});
	$("#tbl_channels tbody tr a").live("click", function(e) { e.stopPropagation(); });
	
	var menu = $('#chanmenu').menu({
		selected: function(event, ui) {
			$(this).hide();
			var chan = encodeURIComponent(menu.data('channel'));
			switch (ui.item.data('action')) {
				case 'irc':
					location.href = 'irc://'+net_roundrobin+':'+net_port+'/'+chan.replace('%23', '');
					break;
				case 'ircs':
					location.href = 'irc://'+net_roundrobin+':+'+net_port_ssl+'/'+chan.replace('%23', '');
					break;
				case 'webchat':
					location.href = service_webchat + chan;
					break;
				case 'mibbit':
					location.href = 'http://widget.mibbit.com/?settings='+service_mibbit+'&server='+net_roundrobin+'&channel='+chan+'&promptPass=true';
					break;
			}
		}
	}).hide().css({position: 'absolute', zIndex: 1});
		
	$('.chanbutton').live('click', function(event) {
		//menu.data('active-button', this);
		menu.data('channel', $(this).parent().parent().attr('id'));
		if (menu.is(':visible') ){
			menu.hide();
			return false;
		}
		menu.menu('deactivate').show();
		menu.position({
			my: "right top",
			at: "right bottom",
			of: this
		});
		$(document).one("click", function() {
			menu.hide();
		});
		return false;
	});
	
});
{/literal}
</script>
{/jsmin}