{extends file="_main.tpl"}

{block name="title" append}Network{/block}

{block name="content"}
<div id="tabs">
	<ul>
		<li><a href="index.php/network/status">Status</a></li>
		<li><a href="index.php/network/countries">Countries</a></li>
		<li><a href="index.php/network/clients">Clients</a></li>
		<li><a href="index.php/network/operators">Operators</a></li>
		<li><a href="index.php/network/history">History</a></li>
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