<?php namespace Fieldset;

use Fieldset\Html\ValidHtmlTag;

class Fieldset extends ValidHtmlTag implements FormElementInterface
{
            
    public function __construct(array $attributes = array())
    {
        parent::__construct('fieldset');
        
        $this->setAttributes($attributes);
    }

    /**
     * 
     * @param type $data
     * @return \Fieldset\Fieldset
     */
    public function populate(array $data)
    {
        foreach ($this->flatten() as $element) 
        {
            if($element instanceof FormElementInterface)
            {
                $element->populate($data);
            }
        }
        
        return $this;
    }
}
