{extends file='test_block_base.tpl'}
This template should not output anything, ignore all Smarty tags but <block>.
{block name=blocksection}--block section ok--{/block}<br>
{'Hello World'}
{block name=blockpassedbysection}--block passed by section false--{/block}<br>
{block name='parentsection'}--section--{/block}<br>
