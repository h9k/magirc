{extends file="_main.tpl"}

{block name="title" append}Support{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/support/docs">Documentation</a></li>
		<li><a href="index.php/support/registration">Registration</a></li>
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
