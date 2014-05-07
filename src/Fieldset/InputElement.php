<?php namespace Fieldset;

use Fieldset\Html\ValidHtmlTag;
use Closure;


class InputElement extends ValidHtmlTag implements FormElementInterface
{

    protected $input;
    
    protected $label;
    
    protected $composer;

    public function __construct($type, array $attributes = array(), $containerTag = 'div')
    {
        isset($attributes['id']) or $attributes['id'] = null;
        
        if(isset($attributes['label']))
        {
            $this->label = $this->tag('label', array(
                'name' => $attributes['label'],
                'for'  => $attributes['id']
            ));
            
            unset($attributes['label']);
        }

        $type = strtolower($type);
        
        $this->input = $this->tag($type);

        if(in_array($type, array('select', 'radio')) && isset($attributes['options']))
        {

            if ($type == 'radio') {
                $this->input = $this->tag('div', array(
                    'data-fieldset' => 'radio-group'
                ));
                $this->input->setOriginalName($type);
            }

            $copy = $attributes;

            unset($copy['options']);

            foreach($attributes['options'] as $k => $v)
            {
                if ($type == 'select') {
                    $this->input->addTag($this->tag('option', array('value' => $k, 'name' => $v)));
                } else {

                    $copy = array('value' => $k,'id' => $attributes['name'] . '-' . $k) + $copy;

                    $this->input->addTag($this->tag('radio', $copy));

                    $this->input->addTag($this->tag('label', $v)->setAttribute('for', $copy['id']));

                    unset($attributes['type']);
                }
            }

            unset($attributes['options']);
        }

        $this->input->setAttributes($attributes);
        
        parent::__construct($containerTag);
    }
    
    public function tag($type, $attributes = array())
    {
        return new ValidHtmlTag($type, $attributes);
    }
    
    public function setComposer(Closure $callback)
    {
        $this->composer = $callback;
        
        return $this;
    }
    
    protected function tagToString($level = 1)
    {
        if($this->composer)
        {
            $return = call_user_func($this->composer, $this, $this->input, $this->label);
            
            $return and $this->addTag($return);
        }
        else
        {
            $this->label and $this->addTag($this->label);
            $this->addTag($this->input);
        }
        
        return parent::tagToString($level);
    }
    
    public function populate(array $data)
    {
        if($name = $this->input->getAttribute('name'))
        {  
            isset($data[$name]) && $this->input->setAttribute('value', $data[$name]);
        }
    }
    
    public function input()
    {
        return $this->input;
    }
    
    public function label()
    {
        return $this->label;
    }
}
