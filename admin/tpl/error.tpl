{extends file="_main.tpl"}

{block name="title" append}{t}Error{/t}{/block}

{block name="content"}
    <div id="errorbox">
        <h1>Something happened!</h1>
        {if $err_code eq 403}
            You don't have the rights to view the requested content.
        {elseif $err_code eq  404}
            The requested resource does not exist.
        {else}
            Unknown error.
        {/if}
        <br /><sub>Code: {$err_code}</sub>
    </div>
{/block}
