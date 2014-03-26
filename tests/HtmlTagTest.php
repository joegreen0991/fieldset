<?php

class HtmlTagTest extends \PHPUnit_Framework_TestCase {

    public function testObjectReturnsInput()
    {        
        $html = new \Fieldset\HtmlTag('div', true);

        $this->assertEquals('<div></div>', $html->render());
        
        
        $html = new \Fieldset\HtmlTag('random', false);

        $this->assertEquals('<random/>', (string)$html);
    }
    
    public function testTagNameCanBeGot()
    {        
        $html = new \Fieldset\HtmlTag('div', true);

        $this->assertEquals('div', $html->getTagName());
    }
    
    public function testChildrenCanBeCounted()
    {        
        // 1
        $html = new \Fieldset\HtmlTag('div', true);
        
        // 2
        $html->addTag(new \Fieldset\HtmlTag('span', true));
        
        // 3
        $subChild = new \Fieldset\HtmlTag('span', true);
        
        // 4 and 5 added to 3
        $subChild->addTag(new \Fieldset\HtmlTag('input'))
                 ->addTag(new \Fieldset\HtmlTag('label', true));
        
        // 3 finally pushed onto the parent (1)
        $html->addTag($subChild);
        
        $this->assertEquals(5, $html->count());
        
        
        $this->assertEquals("<div>\n\t<span></span>\n\t<span>\n\t\t<input/>\n\t\t<label></label>\n\t</span>\n</div>", $html->render());
    }
    
    public function testAttributesCanBeSet()
    {        
        $html = new \Fieldset\HtmlTag('div', true);
        
        $html->setAttribute('class', 'test');
        
        $this->assertEquals('test', $html->getAttribute('class'));
        
        $html->setAttribute('class', 'test2');
        
        $this->assertEquals('test test2', $html->getAttribute('class'));
        
        // Test a non-set attribute returns null
        $this->assertEquals(null, $html->getAttribute('non-existant-class'));
    }
    
    public function testClonedElementsAreNotTheSame()
    {        
        $html = new \Fieldset\HtmlTag('div', true);
        
        $subChild = new \Fieldset\HtmlTag('span', true);
        
        $subChild->addTag(new \Fieldset\HtmlTag('input'))
                 ->addTag(new \Fieldset\HtmlTag('label', true));
        
        $html->addTag($subChild);
        
        $copy = $html;
        
        $this->assertSame($html, $copy);
        
        $clone = clone $html;
        
        $this->assertNotSame($html, $clone);
    }
}