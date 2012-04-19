{extends file="_main.tpl"}

{block name="title" append}{t}Channel{/t}: {$target}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/channel/{$target|escape:'url'}/status" title="status">{t}Status{/t}</a></li>
		<li><a href="index.php/channel/{$target|escape:'url'}/countries" title="countries">{t}Countries{/t}</a></li>
		<li><a href="index.php/channel/{$target|escape:'url'}/clients" title="clients">{t}Clients{/t}</a></li>
		<li><a href="index.php/channel/{$target|escape:'url'}/activity" title="activity">{t}Activity{/t}</a></li>
	</ul>
</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">
var target = '{$target|escape:'url'}';
{literal}
$(document).ready(function() {
	var tabs = $("#tabs").tabs({
		select: function(event, ui) { window.location.hash = ui.tab.hash; },
		cache: true,
		spinner: '{t}Loading{/t}...',
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html(mLang.LoadError);
			}
		}
	});
	$.getJSON('rest/denora.php/channels/'+target+'/checkstats', function(data) {
		if (!data) tabs.tabs("remove", 3);
	});
});
{/literal}
</script>
{/jsmin}
{/block}