{* $Id$ *}
{extends file="components/main.tpl"}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="?section=channel&amp;action=list">Channel list</a></li>
		<li><a href="?section=channel&amp;action=history">Channel history</a></li>
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