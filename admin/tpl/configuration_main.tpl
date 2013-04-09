{extends file="_main.tpl"}

{block name="title" append}Configuration{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li title="welcome"><a href="index.php/configuration/welcome">Welcome</a></li>
		<li title="interface"><a href="index.php/configuration/interface">Interface</a></li>
		<li title="network"><a href="index.php/configuration/network">Network</a></li>
		<li title="services"><a href="index.php/configuration/services">Services</a></li>
		<li title="denora"><a href="index.php/configuration/denora">Denora</a></li>
		{*<li title="admins"><a href="index.php/configuration/admins">Administrators</a></li>*}
	</ul>
</div>
<div id="success">Saved successfully</div>
<div id="failure">Failed</div>
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
