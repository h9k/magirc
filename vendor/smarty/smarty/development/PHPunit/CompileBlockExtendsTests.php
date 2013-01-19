<?php
/**
* Smarty PHPunit tests for Block Extends
*
* @package PHPunit
* @author Uwe Tews
*/

/**
* class for block extends compiler tests
*/
class CompileBlockExtendsTests extends PHPUnit_Framework_TestCase {
    public function setUp()
    {
        $this->smarty = SmartyTests::$smarty;
        SmartyTests::init();
    }

    public static function isRunnable()
    {
        return true;
    }

    /**
    * clear folders
    */
    public function clear()
    {
        $this->smarty->clearAllCache();
        $this->smarty->clearCompiledTemplate();
   }
    /**
    * test block default outout
    */
    public function testBlockDefault1()
    {
        $result = $this->smarty->fetch('eval:{block name=test}-- block default --{/block}');
        $this->assertEquals('-- block default --', $result);
    }

    public function testBlockDefault2()
    {
        $this->smarty->assign ('foo', 'another');
        $result = $this->smarty->fetch('eval:{block name=test}-- {$foo} block default --{/block}');
        $this->assertEquals('-- another block default --', $result);
    }
    /**
    * test just call of  parent template, no blocks predefined
    */
    public function testCompileBlockParent()
    {
        $result = $this->smarty->fetch('test_block_parent.tpl');
        $this->assertContains('Default Title', $result);
    }
    /**
    * test  child/parent template chain
    */
    public function testCompileBlockChild()
    {
        $result = $this->smarty->fetch('test_block_child.tpl');
        $this->assertContains('Page Title', $result);
    }
    /**
    * test  child/parent template chain with prepend
    */
    public function testCompileBlockChildPrepend()
    {
        $result = $this->smarty->fetch('test_block_child_prepend.tpl');
        $this->assertContains("prepend - Default Title", $result);
    }
    /**
    * test  child/parent template chain with apppend
    */
    public function testCompileBlockChildAppend()
    {
        $result = $this->smarty->fetch('test_block_child_append.tpl');
        $this->assertContains("Default Title - append", $result);
    }
    /**
    * test  child/parent template chain with apppend and shorttags
    */
    public function testCompileBlockChildAppendShortag()
    {
        $result = $this->smarty->fetch('test_block_child_append_shorttag.tpl');
        $this->assertContains("Default Title - append", $result);
    }
    /**
    * test  child/parent template chain with {$smarty.block.child)
    */
    public function testCompileBlockChildSmartyChild()
    {
        $result = $this->smarty->fetch('test_block_child_smartychild.tpl');
        $this->assertContains('here is child text included', $result);
    }
    /**
    * test  child/parent template chain with {$smarty.block.parent)
    */
    public function testCompileBlockChildSmartyParent()
    {
        $result = $this->smarty->fetch('test_block_child_smartyparent.tpl');
        $this->assertContains('parent block Default Title is here', $result);
    }
    /**
    * test  child/parent template chain loading plugin
    */
    public function testCompileBlockChildPlugin()
    {
        $result = $this->smarty->fetch('test_block_child_plugin.tpl');
        $this->assertContains('escaped &lt;text&gt;', $result);
    }
    /**
    * test parent template with nested blocks
    */
    public function testCompileBlockParentNested()
    {
        $result = $this->smarty->fetch('test_block_parent_nested.tpl');
        $this->assertContains('Title with -default- here', $result);
    }
    /**
    * test  child/parent template chain with nested block
    */
    public function testCompileBlockChildNested()
    {
        $result = $this->smarty->fetch('test_block_child_nested.tpl');
        $this->assertContains('Title with -content from child- here', $result);
    }
    /**
    * test  child/parent template chain with nested block and include
    */
    public function testCompileBlockChildNestedInclude()
    {
        $result = $this->smarty->fetch('test_block_grandchild_nested_include.tpl');
        $this->assertContains('hello world', $result);
    }
    /**
    * test  grandchild/child/parent template chain
    */
    public function testCompileBlockGrandChild()
    {
        $result = $this->smarty->fetch('test_block_grandchild.tpl');
        $this->assertContains('Grandchild Page Title', $result);
    }
    /**
    * test  grandchild/child/parent template chain prepend
    */
    public function testCompileBlockGrandChildPrepend()
    {
        $result = $this->smarty->fetch('test_block_grandchild_prepend.tpl');
        $this->assertContains('grandchild prepend - Page Title', $result);
    }
    /**
    * test  grandchild/child/parent template chain with {$smarty.block.child}
    */
    public function testCompileBlockGrandChildSmartyChild()
    {
        $result = $this->smarty->fetch('test_block_grandchild_smartychild.tpl');
        $this->assertContains('child title with - grandchild content - here', $result);
    }
    /**
    * test  grandchild/child/parent template chain append
    */
    public function testCompileBlockGrandChildAppend()
    {
        $result = $this->smarty->fetch('test_block_grandchild_append.tpl');
        $this->assertContains('Page Title - grandchild append', $result);
    }
    /**
    * test  grandchild/child/parent template chain with nested block
    */
    public function testCompileBlockGrandChildNested()
    {
        $result = $this->smarty->fetch('test_block_grandchild_nested.tpl');
        $this->assertContains('child title with -grandchild content- here', $result);
    }
    /**
    * test  grandchild/child/parent template chain with nested {$smarty.block.child}
    */
    public function testCompileBlockGrandChildNested3()
    {
        $result = $this->smarty->fetch('test_block_grandchild_nested3.tpl');
        $this->assertContains('child pre -grandchild content- child post', $result);
    }
    /**
    * test  nested child block with hide
    */
    public function testCompileBlockChildNestedHide()
    {
        $result = $this->smarty->fetch('test_block_child_nested_hide.tpl');
        $this->assertContains('nested block', $result);
        $this->assertNotContains('should be hidden', $result);
    }
    /**
    * test  nested child block with hide and auto_literal = false
    */
    public function testCompileBlockChildNestedHideAutoLiteralFalse()
    {
        $this->smarty->auto_literal = false;
        $result = $this->smarty->fetch('test_block_child_nested_hide_space.tpl');
        $this->assertContains('nested block', $result);
        $this->assertNotContains('should be hidden', $result);
    }
    /**
    * test  child/parent template chain starting in subtempates
    */
    public function testCompileBlockStartSubTemplates()
    {
        $result = $this->smarty->fetch('test_block_include_root.tpl');
        $this->assertContains('page 1', $result);
        $this->assertContains('page 2', $result);
        $this->assertContains('page 3', $result);
        $this->assertContains('block 1', $result);
        $this->assertContains('block 2', $result);
        $this->assertContains('block 3', $result);
   }
    /**
    * test  grandchild/child/parent dependency test1
    */
    public function testCompileBlockGrandChildMustCompile1()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->createTemplate('test_block_grandchild.tpl');
        $this->assertFalse($tpl->isCached());
        $result = $this->smarty->fetch($tpl);
        $this->assertContains('Grandchild Page Title', $result);
        $this->smarty->template_objects = null;
        $tpl2 = $this->smarty->createTemplate('test_block_grandchild.tpl');
        $this->assertTrue($tpl2->isCached());
        $result = $this->smarty->fetch($tpl2);
        $this->assertContains('Grandchild Page Title', $result);
    }
    /**
    * test  grandchild/child/parent dependency test2
    */
    public function testCompileBlockGrandChildMustCompile2()
    {
        touch($this->smarty->getTemplateDir(0) . 'test_block_grandchild.tpl');
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->createTemplate('test_block_grandchild.tpl');
        $this->assertFalse($tpl->isCached());
        $result = $this->smarty->fetch($tpl);
        $this->assertContains('Grandchild Page Title', $result);
        $this->smarty->template_objects = null;
        $tpl2 = $this->smarty->createTemplate('test_block_grandchild.tpl');
        $this->assertTrue($tpl2->isCached());
        $result = $this->smarty->fetch($tpl2);
        $this->assertContains('Grandchild Page Title', $result);
    }
    /**
    * test  grandchild/child/parent dependency test3
    */
    public function testCompileBlockGrandChildMustCompile3()
    {
        touch($this->smarty->getTemplateDir(0) . 'test_block_child.tpl');
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->createTemplate('test_block_grandchild.tpl');
        $this->assertFalse($tpl->isCached());
        $result = $this->smarty->fetch($tpl);
        $this->assertContains('Grandchild Page Title', $result);
        $this->smarty->template_objects = null;
        $tpl2 = $this->smarty->createTemplate('test_block_grandchild.tpl');
        $this->assertTrue($tpl2->isCached());
        $result = $this->smarty->fetch($tpl2);
        $this->assertContains('Grandchild Page Title', $result);
    }
    /**
    * test  grandchild/child/parent dependency test4
    */
    public function testCompileBlockGrandChildMustCompile4()
    {
        touch($this->smarty->getTemplateDir(0) . 'test_block_parent.tpl');
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->createTemplate('test_block_grandchild.tpl');
        $this->assertFalse($tpl->isCached());
        $result = $this->smarty->fetch($tpl);
        $this->assertContains('Grandchild Page Title', $result);
        $this->smarty->template_objects = null;
        $tpl2 = $this->smarty->createTemplate('test_block_grandchild.tpl');
        $this->assertTrue($tpl2->isCached());
        $result = $this->smarty->fetch($tpl2);
        $this->assertContains('Grandchild Page Title', $result);
    }
    public function testCompileBlockSection()
    {
        $result = $this->smarty->fetch('test_block_section.tpl');
        $this->assertContains('--block base ok--', $result);
        $this->assertContains('--block section ok--', $result);
        $this->assertContains('--block passed by section false--', $result);
        $this->assertContains('--block root false--', $result);
        $this->assertContains('--block assigned false--', $result);
        $this->assertContains('--section--', $result);
        $this->assertContains('--base--', $result);
        $this->assertContains('--block include false--', $result);
    }
    public function testCompileBlockRoot()
    {
        $this->smarty->assign('foo', 'hallo');
        $result = $this->smarty->fetch('test_block.tpl');
        $this->assertContains('--block base ok--', $result);
        $this->assertContains('--block section ok--', $result);
        $this->assertContains('--block passed by section ok--', $result);
        $this->assertContains('--block root ok--', $result);
        $this->assertContains('--assigned hallo--', $result);
        $this->assertContains('--parent from --section-- block--', $result);
        $this->assertContains('--parent from --base-- block--', $result);
        $this->assertContains('--block include ok--', $result);
    }
    public function testCompileBlockRoot2()
    {
        $this->smarty->assign('foo', 'hallo');
        $result = $this->smarty->fetch('test_block.tpl');
        $this->assertContains('--block base ok--', $result);
        $this->assertContains('--block section ok--', $result);
        $this->assertContains('--block passed by section ok--', $result);
        $this->assertContains('--block root ok--', $result);
        $this->assertContains('--assigned hallo--', $result);
        $this->assertContains('--parent from --section-- block--', $result);
        $this->assertContains('--parent from --base-- block--', $result);
        $this->assertContains('--block include ok--', $result);
    }
    public function testCompileBlockNocacheMain1()
    {
        $this->smarty->assign('foo', 1);
        $this->smarty->caching = 1;
        $this->assertContains('foo 1', $this->smarty->fetch('test_block_nocache_child.tpl'));
    }
    public function testCompileBlockNocacheMain2()
    {
        $this->smarty->assign('foo', 2);
        $this->smarty->caching = 1;
        $this->assertTrue($this->smarty->isCached('test_block_nocache_child.tpl'));
        $this->assertContains('foo 2', $this->smarty->fetch('test_block_nocache_child.tpl'));
    }
    public function testCompileBlockNocacheChild1()
    {
        $this->smarty->assign('foo', 1);
        $this->smarty->caching = 1;
        $this->assertContains('foo 1', $this->smarty->fetch('extends:test_block_nocache_parent.tpl|test_block_nocache_child.tpl'));
    }
    public function testCompileBlockNocacheChild2()
    {
        $this->smarty->assign('foo', 2);
        $this->smarty->caching = 1;
        $this->assertTrue($this->smarty->isCached('extends:test_block_nocache_parent.tpl|test_block_nocache_child.tpl'));
        $this->assertContains('foo 2', $this->smarty->fetch('extends:test_block_nocache_parent.tpl|test_block_nocache_child.tpl'));
    }
}

?>