{* $Id$ *}
{extends file="components/main.tpl"}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="?section=user&amp;action=info&amp;nick={$smarty.get.nick|escape:'url'}">User info</a></li>
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