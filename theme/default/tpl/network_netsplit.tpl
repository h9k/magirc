<h1>Netsplit.de Graphs</h1>

<form>
	<div id="netsplit_type" class="choser">
		<input type="radio" id="type0" name="type" checked="checked" /><label for="type0">Last two weeks</label>
		<input type="radio" id="type1" name="type" /><label for="type1">Last two months</label>
		<input type="radio" id="type2" name="type" /><label for="type2">Last two years</label>
		<input type="radio" id="type3" name="type" /><label for="type3">Complete history</label>
	</div>
</form>

<div id="netsplit_normal" style="width:560px; margin:auto;">
	<h2>Relation of users and channels during the last 2 <span class="netsplit_range">weeks</span></h2>
	<img id="netsplit_relation" src="" alt="" />
	<h2>Channels during the last 2 <span class="netsplit_range">weeks</span></h2>
	<img id="netsplit_channels" src="" alt="" />
	<h2>Servers during the last 2 <span class="netsplit_range">weeks</span></h2>
	<img id="netsplit_servers" src="" alt="" />
</div>

<div id="netsplit_complete" style="width:560px; margin:auto; display:none">
	<h2>Complete measured history about users and channels</h2>
	<img id="netsplit_history" src="" alt="" />
</div>

<br />More on <a href="http://irc.netsplit.de/networks/{$cfg.service_netsplit}/" rel="external" target="_blank">Netsplit.de</a>

{jsmin}
<script type="text/javascript">
var netsplit = '{$cfg.service_netsplit}';
{literal}
$(document).ready(function() {
	var types_lang = { 'weeks': 'weeks', 'months': 'months', 'years': 'years' };
	var types = [ 'weeks', 'months', 'years', 'history' ];
	var type = types[0];
	$("#netsplit_type").buttonset();
    $("#netsplit_type").change(function() {
		type = types[$('input[name=type]:checked').index() / 2];
		updateNetsplit(type);
	});
	function updateNetsplit(type) {
		if (type == 'history') {
			$("#netsplit_normal").hide();
			$("#netsplit_complete").show();
			$("#netsplit_history").attr('src', 'http://irc.netsplit.de/tmp/networks/history_'+netsplit+'_uc.png');
		} else {
			$("#netsplit_complete").hide();
			$("#netsplit_normal").show();
			$("#netsplit_relation").attr('src', 'http://irc.netsplit.de/tmp/networks/'+type+'_'+netsplit+'_uc.png');
			$("#netsplit_channels").attr('src', 'http://irc.netsplit.de/tmp/networks/'+type+'_'+netsplit+'_uc.png');
			$("#netsplit_servers").attr('src', 'http://irc.netsplit.de/tmp/networks/'+type+'_'+netsplit+'_uc.png');
			$(".netsplit_range").html(types_lang[type]);
		}
	}
	updateNetsplit(type);
});
{/literal}
</script>
{/jsmin}