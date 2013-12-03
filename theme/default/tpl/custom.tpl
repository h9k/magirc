{extends file="_main.tpl"}

{block name="title" append}Customization example{/block}

{block name="content"}
	<div style="margin:20px;">
		<h1>{$example}!</h1>
		
		<ul>
		{foreach $channels as $channel}
			<li>{$channel->channel}</li>
		{/foreach}
		</ul>
	</div>
{/block}

{block name="js" append}
{jsmin}
<script type="text/javascript">

</script>
{/jsmin}
{/block}