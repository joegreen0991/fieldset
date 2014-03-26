<?php

class InputElementTest extends \PHPUnit_Framework_TestCase {

    public function testObjectReturnsInput()
    {        
        $text = new \Fieldset\InputElement('text', array(
            'name' => 'hello',
            'class' => 'world'
        ));

        $this->assertEquals('<div>' . "\n\t" . '<input type="text" name="hello" class="world"/>' . "\n" . '</div>', $text->render());
    }
    
    public function testObjectReturnsLabeledInput()
    {        
        $text = new \Fieldset\InputElement('text', array(
            'name' => 'hello',
            'class' => 'world',
            'label' => 'Hello World'
        ));
        
        $rendered = $text->render();

        $this->assertEquals('<div>'  . "\n\t" . '<label>Hello World</label>' . "\n\t" . '<input type="text" name="hello" class="world"/>' . "\n" . '</div>', $rendered);
    }

    public function testObjectReturnsCustomInput()
    {        
        $text = new \Fieldset\InputElement('text', array(
            'name' => 'hello',
            'class' => 'world',
        ));
        
        $rendered = $text->render(function($text, $input){
            $text->addTag($input);
            $text->addTag($text->tag('label','TestLabel'));
        });

        $this->assertEquals('<div>' . "\n\t" . '<input type="text" name="hello" class="world"/>' . "\n\t" . '<label>TestLabel</label>'. "\n" . '</div>', $rendered);
    }
}