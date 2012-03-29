{extends file="_main.tpl"}

{block name="title" append}Denora{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/denora/general">General</a></li>
		<li><a href="index.php/denora/network">Network</a></li>
		<li><a href="index.php/denora/database">Database</a></li>
		<li><a href="index.php/denora/advanced">Advanced</a></li>
	</ul>
</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript"><!--{literal}
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
