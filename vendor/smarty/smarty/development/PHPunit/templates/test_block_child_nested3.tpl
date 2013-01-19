{extends file='test_block_parent3.tpl'} 
{block name='content1'}
 {block name='content2'}
  child pre {$smarty.block.child} child post
 {/block}
{/block} 