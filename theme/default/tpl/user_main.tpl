{extends file="_main.tpl"}

{block name="title" append}Users{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/user/globalactivity">Activity</a></li>
		<li><a href="index.php/user/history">History</a></li>
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