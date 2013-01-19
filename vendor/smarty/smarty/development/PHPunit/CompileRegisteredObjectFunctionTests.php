<?php
/**
* Smarty PHPunit tests compilation of registered object functions
*
* @package PHPunit
* @author Uwe Tews
*/

/**
* class for registered object function tests
*/
class CompileRegisteredObjectFunctionTests extends PHPUnit_Framework_TestCase {
    public function setUp()
    {
        $this->smarty = SmartyTests::$smarty;
        SmartyTests::init();
        $this->smarty->force_compile = true;
        $this->smarty->disableSecurity();
        $this->object = new RegObject;
        $this->smarty->registerObject('objecttest', $this->object, 'myhello', true, 'myblock');
    }

    public static function isRunnable()
    {
        return true;
    }

    /**
    * test resgistered object as function
    */
    public function testRegisteredObjectFunction()
    {
        $tpl = $this->smarty->createTemplate('eval:{objecttest->myhello}');
        $this->assertEquals('hello world', $this->smarty->fetch($tpl));
    }
    /**
    * test resgistered object as function with modifier
    */
    public function testRegisteredObjectFunctionModifier()
    {
        $tpl = $this->smarty->createTemplate('eval:{objecttest->myhello|truncate:6}');
        $this->assertEquals('hel...', $this->smarty->fetch($tpl));
    }

    /**
    * test resgistered object as block function
    */
    public function testRegisteredObjectBlockFunction()
    {
        $tpl = $this->smarty->createTemplate('eval:{objecttest->myblock}hello world{/objecttest->myblock}');
        $this->assertEquals('block test', $this->smarty->fetch($tpl));
    }
    public function testRegisteredObjectBlockFunctionModifier1()
    {
        $tpl = $this->smarty->createTemplate('eval:{objecttest->myblock}hello world{/objecttest->myblock|strtoupper}');
        $this->assertEquals(strtoupper('block test'), $this->smarty->fetch($tpl));
    }
    public function testRegisteredObjectBlockFunctionModifier2()
    {
        $tpl = $this->smarty->createTemplate('eval:{objecttest->myblock}hello world{/objecttest->myblock|default:""|strtoupper}');
        $this->assertEquals(strtoupper('block test'), $this->smarty->fetch($tpl));
    }
}

Class RegObject {
    function myhello($params)
    {
        return 'hello world';
    }
    function myblock($params, $content, &$smarty_tpl, &$repeat)
    {
        if (isset($content)) {
            $output = str_replace('hello world', 'block test', $content);
            return $output;
        }
    }
}

?>