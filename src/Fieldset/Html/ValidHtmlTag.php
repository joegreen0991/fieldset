<?php namespace Fieldset\Html;

class ValidHtmlTag extends HtmlTag {

    protected $fieldTypes = array(
        'text' => 'input',
        'number' => 'input',
        'date' => 'input',
        'time' => 'input',
        'range' => 'input',
        'submit' => 'input',
        'reset' => 'input',
        'checkbox' => 'input',
        'radio' => 'input',
        'password' => 'input',
        'hidden' => 'input',
    );
    protected $fieldsHavingTypesAttr = array('input');
    protected $fieldsNotContainerTypes = array('input');

    public function __construct($type, $attributes = null)
    {

        $tagName = isset($this->fieldTypes[$type]) ? $this->fieldTypes[$type] : $type;

        $container = !in_array($tagName, $this->fieldsNotContainerTypes);

        parent::__construct($tagName, $container);

        if (in_array(strtolower($tagName), $this->fieldsHavingTypesAttr))
        {
            $this->setAttribute('type', $type);
        }

        is_array($attributes) or $attributes = array('name' => $attributes);

        if(isset($attributes['name']))
        {
            $container ? $this->addText($attributes['name']) : $this->setAttribute('name', $attributes['name']);
            unset($attributes['name']);
        }
        
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }
    }

}
