{extends file="_main.tpl"}

{block name="title" append}Support{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/support/doc/readme">README</a></li>
		<li><a href="index.php/support/doc/support">Resources</a></li>
		<li><a href="index.php/support/register">Registration</a></li>
		<li><a href="index.php/support/doc/changelog">Changelog</a></li>
		<li><a href="index.php/support/doc/credits">Credits</a></li>
		<li><a href="index.php/support/doc/license">License</a></li>
	</ul>
</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript"><!--{literal}
$(function() {
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
--></script>
{/jsmin}
{/block}
