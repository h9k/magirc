{extends file="_main.tpl"}

{block name="title" append}Channel: {$target}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/channel/{$target|escape:'url'}/status" title="status">Status</a></li>
		<li><a href="index.php/channel/{$target|escape:'url'}/countries" title="countries">Countries</a></li>
		<li><a href="index.php/channel/{$target|escape:'url'}/clients" title="clients">Clients</a></li>
		<li><a href="index.php/channel/{$target|escape:'url'}/activity" title="activity">Activity</a></li>
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