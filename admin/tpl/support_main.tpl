{extends file="_main.tpl"}

{block name="title" append}Support{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/support/doc/readme" title="readme">README</a></li>
		<li><a href="index.php/support/doc/support" title="resources">Resources</a></li>
		<li><a href="index.php/support/register" title="registration">Registration</a></li>
		<li><a href="index.php/support/doc/credits" title="credits">Credits</a></li>
		<li><a href="index.php/support/doc/license" title="license">License</a></li>
	</ul>
</div>
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
