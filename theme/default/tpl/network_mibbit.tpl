<h1>{t}Mibbit Graphs{/t}</h1>

<form>
	<div id="mibbit_type" class="choser">
		<input type="radio" id="type0" name="type" checked="checked" /><label for="type0">{t}Day{/t}</label>
		<input type="radio" id="type1" name="type" /><label for="type1">{t}Week{/t}</label>
		<input type="radio" id="type2" name="type" /><label for="type2">{t}Month{/t}</label>
		<input type="radio" id="type3" name="type" /><label for="type3">{t}Year{/t}</label>
	</div>
</form>

<div style="width:600px; margin:auto;">
	<h2><span id="mibbit_range_relation"></span></h2>
	<img id="mibbit_relation" src="" alt="" />
</div>

<br />{t}More on{/t} <a href="http://search.mibbit.com/networks/{$cfg->net_name}" rel="external" target="_blank">Mibbit</a>

{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
	var types_lang_relation = { 'day': mLang.MibbitRelDay, 'week': mLang.MibbitRelWeek, 'month': mLang.MibbitRelMonth, 'year': mLang.MibbitRelYear };
	var types = [ 'day', 'week', 'month', 'year' ];
	var type = types[0];
	$("#mibbit_type").buttonset();
    $("#mibbit_type").change(function() {
		type = types[$('input[name=type]:checked').index() / 2];
		updateMibbit(type);
	});
	function updateMibbit(type) {
		$("#mibbit_relation").attr('src', 'http://netgraphs.mibbit.com/graphs/'+mibbitid+'_'+type+'.png');
		$("#mibbit_range_relation").html(types_lang_relation[type]);
	}
	updateMibbit(type);
});
{/literal}
</script>
{/jsmin}