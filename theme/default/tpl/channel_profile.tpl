{extends file="_main.tpl"}

{block name="title" append}{t}Channel{/t}: {$target}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li title="status"><a href="index.php/channel/{$target|escape:'url'}/status">{t}Status{/t}</a></li>
		<li title="countries"><a href="index.php/channel/{$target|escape:'url'}/countries">{t}Countries{/t}</a></li>
		<li title="clients"><a href="index.php/channel/{$target|escape:'url'}/clients">{t}Clients{/t}</a></li>
		<li title="activity"><a href="index.php/channel/{$target|escape:'url'}/activity">{t}Activity{/t}</a></li>
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
		beforeActivate: function(event, ui) {
			window.location.hash = ui.newTab.attr('title');
		},
		beforeLoad: function(event, ui) {
			if (window.location.hash) {
				var title = window.location.hash.substring(1, window.location.hash.length);
				$("li[title='"+title+"'] a").trigger("click");
			}			
			if (ui.tab.data("loaded")) {
				event.preventDefault();
				return;
			}
			ui.jqXHR.success(function() {
				ui.tab.data("loaded", true);
			});
			ui.jqXHR.error(function() {
				ui.panel.html(mLang.LoadError);
			});
		}
	});
	$.getJSON('rest/service.php/channels/'+target+'/checkstats', function(data) {
		if (!data) tabs.tabs("disable", 3);
	});
});
{/literal}
</script>
{/jsmin}
{/block}