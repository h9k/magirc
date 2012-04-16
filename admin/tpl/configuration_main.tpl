{extends file="_main.tpl"}

{block name="title" append}Configuration{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/configuration/welcome" title="welcome">Welcome</a></li>
		<li><a href="index.php/configuration/interface" title="interface">Interface</a></li>
		<li><a href="index.php/configuration/network" title="network">Network</a></li>
		<li><a href="index.php/configuration/services" title="services">Services</a></li>
		<li><a href="index.php/configuration/denora" title="denora">Denora</a></li>
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
		select: function(event, ui) { window.location.hash = ui.tab.hash; },
		cache: true,
		spinner: 'Loading...',
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html("Unable to load contents");
			}
		}
	});
});
{/literal}
</script>
{/jsmin}
{/block}
