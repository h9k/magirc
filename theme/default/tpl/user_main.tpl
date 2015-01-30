{extends file="_main.tpl"}

{block name="title" append}{t}Users{/t}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li title="activity"><a href="index.php/user/globalactivity">{t}Activity{/t}</a></li>
        <li title="history"><a href="index.php/user/history">{t}History{/t}</a></li>
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
				ui.panel.text(mLang.LoadError);
			});
		}
	});
});
{/literal}
</script>
{/jsmin}
{/block}