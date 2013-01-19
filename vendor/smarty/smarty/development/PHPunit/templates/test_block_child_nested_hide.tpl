{extends file="test_block_parent_nested2.tpl"}

{block name="index"}
   {block name="test2"}
      nested block.
      {$smarty.block.child}
   {/block}
   {block name="test" hide}
      I should be hidden.
      {$smarty.block.child}
   {/block}
{/block} 