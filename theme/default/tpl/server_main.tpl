{* $Id$ *}
{extends file="components/main.tpl"}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="?section=server&amp;action=list">Server list</a></li>
		<li><a href="?section=server&amp;action=history">Server history</a></li>
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