{extends file="_main.tpl"}

{block name="title" append}User: {$target}{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/user/{$mode}:{$target|escape:'url'}/info">Info</a></li>
		<li><a href="index.php/user/{$mode}:{$target|escape:'url'}/activity">Activity</a></li>
	</ul>
</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript"><!--
{literal}
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
{/literal}
--></script>
{/jsmin}
{/block}