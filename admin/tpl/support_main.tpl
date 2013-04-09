{extends file="_main.tpl"}

{block name="title" append}Support{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li title="readme"><a href="index.php/support/doc/readme">README</a></li>
		<li title="resources"><a href="index.php/support/doc/support">Resources</a></li>
		<li title="registration"><a href="index.php/support/register">Registration</a></li>
		<li title="credits"><a href="index.php/support/doc/credits">Credits</a></li>
		<li title="license"><a href="index.php/support/doc/license">License</a></li>
	</ul>
</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">{literal}
$(document).ready(function() {
	$( "#tabs" ).tabs({
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
