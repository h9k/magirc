{extends file="_main.tpl"}

{block name="title" append}{t}Channels{/t}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li title="channels"><a href="index.php/channel/list">{t}Channels{/t}</a></li>
		<li title="activity"><a href="index.php/channel/globalactivity">{t}Activity{/t}</a></li>
        {if $cfg->service eq 'denora'}<li title="history"><a href="index.php/channel/history">{t}History{/t}</a></li>{/if}
	</ul>
</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">
{literal}
$(document).ready(function() {
	$("#tabs").tabs({
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
});
{/literal}
</script>
{/jsmin}
{/block}