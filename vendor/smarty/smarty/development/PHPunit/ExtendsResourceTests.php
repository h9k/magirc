<?php
/**
* Smarty PHPunit tests for Extendsresource
*
* @package PHPunit
* @author Uwe Tews
*/


/**
* class for extends resource tests
*/
class ExtendsResourceTests extends PHPUnit_Framework_TestCase {
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
    /* Test compilation */
    public function testExtendsResourceBlockBase()
    {
        $this->smarty->force_compile=true;
        $result = $this->smarty->fetch('extends:test_block_base.tpl');
        $this->assertContains('--block base ok--', $result);
        $this->assertContains('--block section false--', $result);
        $this->assertContains('--block passed by section false--', $result);
        $this->assertContains('--block root false--', $result);
        $this->assertContains('--block assigned false--', $result);
        $this->assertContains('--parent from section false--', $result);
        $this->assertContains('--base--', $result);
        $this->assertContains('--block include false--', $result);
    }
    public function testExtendResourceBlockSection()
    {
        $this->smarty->force_compile=true;
        $result = $this->smarty->fetch('extends:test_block_base.tpl|test_block_section.tpl');
        $this->assertContains('--block base ok--', $result);
        $this->assertContains('--block section ok--', $result);
        $this->assertContains('--block passed by section false--', $result);
        $this->assertContains('--block root false--', $result);
        $this->assertContains('--block assigned false--', $result);
        $this->assertContains('--section--', $result);
        $this->assertContains('--base--', $result);
        $this->assertContains('--block include false--', $result);
    }
    public function testExtendResourceBlockRoot()
    {
        $this->smarty->force_compile=true;
        $this->smarty->assign('foo', 'hallo');
        $result = $this->smarty->fetch('extends:test_block_base.tpl|test_block_section.tpl|test_block.tpl');
        $this->assertContains('--block base ok--', $result);
        $this->assertContains('--block section ok--', $result);
        $this->assertContains('--block passed by section ok--', $result);
        $this->assertContains('--block root ok--', $result);
        $this->assertContains('--assigned hallo--', $result);
        $this->assertContains('--parent from --section-- block--', $result);
        $this->assertContains('--parent from --base-- block--', $result);
        $this->assertContains('--block include ok--', $result);
    }
    public function testExtendsTagWithExtendsResource()
    {
        $this->smarty->force_compile=true;
        $this->smarty->assign('foo', 'hallo');
        $result = $this->smarty->fetch('test_block_extends.tpl');
        $this->assertContains('--block base from extends--', $result);
        $this->assertContains('--block section ok--', $result);
        $this->assertContains('--block passed by section ok--', $result);
        $this->assertContains('--block root ok--', $result);
        $this->assertContains('--assigned hallo--', $result);
        $this->assertContains('--parent from --section-- block--', $result);
        $this->assertContains('--parent from --base-- block--', $result);
        $this->assertContains('--block include ok--', $result);
    }
    /**
    * test  grandchild/child/parent dependency test1
    */
    public function testCompileBlockGrandChildMustCompile1()
    {
        // FIXME: this tests fails when run with smartytestssingle.php
        // $this->smarty->clearCache('extends:test_block_parent.tpl|test_block_child_resource.tpl|test_block_grandchild_resource.tpl');
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->createTemplate('extends:test_block_parent.tpl|test_block_child_resource.tpl|test_block_grandchild_resource.tpl');
        $this->assertFalse($tpl->isCached());
        $result = $this->smarty->fetch($tpl);
        $this->assertContains('Grandchild Page Title', $result);
        $this->smarty->template_objects = null;
        $tpl2 = $this->smarty->createTemplate('extends:test_block_parent.tpl|test_block_child_resource.tpl|test_block_grandchild_resource.tpl');
        $this->assertTrue($tpl2->isCached());
        $result = $this->smarty->fetch($tpl2);
        $this->assertContains('Grandchild Page Title', $result);
    }
    /**
    * test  grandchild/child/parent dependency test2
    */
    public function testCompileBlockGrandChildMustCompile2()
    {
        touch($this->smarty->getTemplateDir(0) . 'test_block_grandchild_resource.tpl');
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->createTemplate('extends:test_block_parent.tpl|test_block_child_resource.tpl|test_block_grandchild_resource.tpl');
        $this->assertFalse($tpl->isCached());
        $result = $this->smarty->fetch($tpl);
        $this->assertContains('Grandchild Page Title', $result);
        $this->smarty->template_objects = null;
        $tpl2 = $this->smarty->createTemplate('extends:test_block_parent.tpl|test_block_child_resource.tpl|test_block_grandchild_resource.tpl');
        $this->assertTrue($tpl2->isCached());
        $result = $this->smarty->fetch($tpl2);
        $this->assertContains('Grandchild Page Title', $result);
     }
    /**
    * test  grandchild/child/parent dependency test3
    */
    public function testCompileBlockGrandChildMustCompile3()
    {
        touch($this->smarty->getTemplateDir(0) . 'test_block_child_resource.tpl');
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->createTemplate('extends:test_block_parent.tpl|test_block_child_resource.tpl|test_block_grandchild_resource.tpl');
        $this->assertFalse($tpl->isCached());
        $result = $this->smarty->fetch($tpl);
        $this->assertContains('Grandchild Page Title', $result);
        $this->smarty->template_objects = null;
        $tpl2 = $this->smarty->createTemplate('extends:test_block_parent.tpl|test_block_child_resource.tpl|test_block_grandchild_resource.tpl');
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
        $tpl = $this->smarty->createTemplate('extends:test_block_parent.tpl|test_block_child_resource.tpl|test_block_grandchild_resource.tpl');
        $this->assertFalse($tpl->isCached());
        $result = $this->smarty->fetch($tpl);
        $this->assertContains('Grandchild Page Title', $result);
        $this->smarty->template_objects = null;
        $tpl2 = $this->smarty->createTemplate('extends:test_block_parent.tpl|test_block_child_resource.tpl|test_block_grandchild_resource.tpl');
        $this->assertTrue($tpl2->isCached());
        $result = $this->smarty->fetch($tpl2);
        $this->assertContains('Grandchild Page Title', $result);
     }
    /**
    * test  nested child block with hide and auto_literal = false
    */
    public function testCompileBlockChildNestedHideAutoLiteralFalseResource()
    {
        $this->smarty->auto_literal = false;
        $result = $this->smarty->fetch('extends:test_block_parent_nested2_space.tpl|test_block_child_nested_hide_space.tpl');
        $this->assertContains('nested block', $result);
        $this->assertNotContains('should be hidden', $result);
    }

    /* Test create cache file */
    public function testExtendResource1()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->assign('foo', 'hallo');
        $result = $this->smarty->fetch('extends:test_block_base.tpl|test_block_section.tpl|test_block.tpl');
        $this->assertContains('--block base ok--', $result);
        $this->assertContains('--block section ok--', $result);
        $this->assertContains('--block passed by section ok--', $result);
        $this->assertContains('--block root ok--', $result);
        $this->assertContains('--assigned hallo--', $result);
        $this->assertContains('--parent from --section-- block--', $result);
        $this->assertContains('--parent from --base-- block--', $result);
        $this->assertContains('--block include ok--', $result);
    }
    /* Test access cache file */
    public function testExtendResource2()
    {
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $this->smarty->assign('foo', 'world');
        $tpl = $this->smarty->createTemplate('extends:test_block_base.tpl|test_block_section.tpl|test_block.tpl');
        $this->assertTrue($this->smarty->isCached($tpl));
        $result = $this->smarty->fetch('extends:test_block_base.tpl|test_block_section.tpl|test_block.tpl');
        $this->assertContains('--block base ok--', $result);
        $this->assertContains('--block section ok--', $result);
        $this->assertContains('--block passed by section ok--', $result);
        $this->assertContains('--block root ok--', $result);
        $this->assertContains('--assigned hallo--', $result);
        $this->assertContains('--parent from --section-- block--', $result);
        $this->assertContains('--parent from --base-- block--', $result);
        $this->assertContains('--block include ok--', $result);
    }

    public function testExtendExists()
    {
        $this->smarty->caching = false;
        $tpl = $this->smarty->createTemplate('extends:test_block_base.tpl');
        $this->assertTrue($tpl->source->exists);

        $tpl = $this->smarty->createTemplate('extends:does-not-exists.tpl|this-neither.tpl');
        $this->assertFalse($tpl->source->exists);
    }
}

?>
