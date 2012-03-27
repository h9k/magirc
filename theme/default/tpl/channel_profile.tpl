
{extends file="_main.tpl"}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="?section=channel&amp;action=status&amp;chan={$smarty.get.chan|escape:'url'}">Status</a></li>
		<li><a href="?section=channel&amp;action=countries&amp;chan={$smarty.get.chan|escape:'url'}">Countries</a></li>
		<li><a href="?section=channel&amp;action=clients&amp;chan={$smarty.get.chan|escape:'url'}">Clients</a></li>
		<li><a href="?section=channel&amp;action=activity&amp;chan={$smarty.get.chan|escape:'url'}">Activity</a></li>
	</ul>
</div>
{/block}

{block name="js" append}
<script type="text/javascript">
$(function() {
	$( "#tabs" ).tabs({
		cache: true,
		spinner: 'Loading...',
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html("Unable to load contents");
			}
		}
	});
});
</script>
{/block}