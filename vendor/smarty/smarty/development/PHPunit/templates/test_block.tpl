{extends file='test_block_section.tpl'}
{block name=blockpassedbysection}--block passed by section ok--{/block}<br>
{block name=blockroot}--block root ok--{/block}<br>
{block name="blockassigned"}--assigned {$foo}--{/block}<br>
{block name='parentbase'}--parent from {$smarty.block.parent} block--{/block}<br>
{block name='parentsection'}--parent from {$smarty.block.parent} block--{/block}<br>
{block name='blockinclude'}--block include ok--{/block}<br>
