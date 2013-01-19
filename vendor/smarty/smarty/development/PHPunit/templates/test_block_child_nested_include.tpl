{extends file='test_block_parent_nested_include.tpl'} 
{block name='body'}
{block name='title_content'}{block name='nested'}{include file='helloworld.tpl'}{/block}{/block}
{/block}