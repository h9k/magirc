{extends file="_main.tpl"}

{block name="title" append}{t}Server{/t}: {$target}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li title="info"><a href="index.php/server/{$target|escape:'url'}/info">{t}Info{/t}</a></li>
		<li title="countries"><a href="index.php/server/{$target|escape:'url'}/countries">{t}Countries{/t}</a></li>
		<li title="clients"><a href="index.php/server/{$target|escape:'url'}/clients">{t}Clients{/t}</a></li>
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
				ui.panel.text(mLang.LoadError);
			});
		}
	});
});
{/literal}
</script>
{/jsmin}
{/block}