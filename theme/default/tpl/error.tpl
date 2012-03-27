
{extends file="_main.tpl"}
{block name="css"}<link href="theme/default/css/bsod.css" rel="stylesheet" type="text/css" />{/block}
{block name="body"}
*** STOP: {$err_msg}
<br /><br />
{if $smarty.const.DEBUG}
{foreach from=$server item=item key=key}
{$key}: {$item}<br />
{/foreach}
{else}
This should not have happened. Please contact the Administrator.<br />
If you are the Administrator, please enable Debug mode to see more information<br />
and submit it to the MagIRC developers. Thank you!
{/if}
{/block}