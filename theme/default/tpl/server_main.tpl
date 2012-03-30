{extends file="_main.tpl"}

{block name="title" append}Servers{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/server/list">Servers</a></li>
		<li><a href="index.php/server/history">History</a></li>
	</ul>
</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript"><!--
{literal}
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