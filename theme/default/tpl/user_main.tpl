{* $Id$ *}
{extends file="_main.tpl"}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="?section=user&amp;action=globalactivity">Activity</a></li>
		<li><a href="?section=user&amp;action=history">History</a></li>
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