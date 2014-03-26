<?php

class ValidHtmlTagTest extends \PHPUnit_Framework_TestCase {

    public function testObjectReturnsInput()
    {        
        $html = new \Fieldset\ValidHtmlTag('text');

        $this->assertEquals('<input type="text"/>',$html->render());
    }

    public function testTextareaContainer()
    {
        $html = new \Fieldset\ValidHtmlTag('textarea');

         $this->assertEquals('<textarea></textarea>',$html->render());
    }
    
    public function testDivContainerWithConstructorAttributes()
    {
        $html = new \Fieldset\ValidHtmlTag('div', array(
            'name' => 'content',
            'style' => 'width:50px;'
        ));

        $html->setAttribute('class','testclass');
        
        $this->assertEquals('<div style="width:50px;" class="testclass">content</div>',$html->render());
    }
    
    public function testDivContainerWithAttributes()
    {
        $html = new \Fieldset\ValidHtmlTag('div');

        $html->setAttribute('class','testclass');
        
        $this->assertEquals('<div class="testclass"></div>',$html->render());
    }

}