{extends file="_main.tpl"}

{block name="title" append}Channels{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/channel/list">Channels</a></li>
		<li><a href="index.php/channel/globalactivity">Activity</a></li>
		<li><a href="index.php/channel/history">History</a></li>
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