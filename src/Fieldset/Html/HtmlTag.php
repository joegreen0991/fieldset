<?php namespace Fieldset\Html;

class HtmlTag {


    private $tagOpen = '<[@TAG][@ATTR]';
    private $tagClose = '/>';
    private $tagContOpen = '<[@TAG][@ATTR]>';
    private $tagContClose = '</[@TAG]>';
    private $tagName = '';
    private $originalTagName = null;
    private $tagAttr = array();
    private $tagContainer = false; //boolean defines if tag is containing other tags
    protected $tagChildren = array();
    protected $tagText = null;

    public function __construct($name, $container = false)
    {
        $this->setName($name);
        $this->setContainer($container);
    }
    

    public function setName($name)
    {
        $this->tagName = $name;
        
        return $this;
    }

    public function setOriginalName($name)
    {
        $this->originalTagName = $name;

        return $this;
    }

    public function getTagName()
    {
        return $this->tagName;
    }

    public function getOriginalTagName()
    {
        return $this->originalTagName ?: $this->tagName;
    }
    
    public function setContainer($bool = true)
    {
        $this->tagContainer = $bool;
        
        return $this;
    }
    
    public function setAttributes(array $attributes)
    {
        foreach($attributes as $attribute => $value)
        {
            $this->setAttribute($attribute, $value);
        }
        
        return $this;
    }
    
    public function appendAttribute($name, $value = null)
    {
        if (isset($this->tagAttr[$name]))
        {
            $attr = $this->tagAttr[$name] . ' ' . $value;
            $value = $this->removeDuplicateWords($attr);
        }

        $this->tagAttr[$name] = $value;
        
        return $this;
    }

    public function setAttribute($name, $value = null)
    {

        $this->tagAttr[$name] = $value;
        
        return $this;
    }
    
    public function getAttribute($name)
    {
        if (isset($this->tagAttr[$name]))
        {
            return $this->tagAttr[$name];
        }
    }

    public function addTag(HtmlTag $tag)
    {
        $this->tagContainer = true;

        $this->tagChildren[] = $tag;

        return $this;
    }

    public function addText($text)
    {
        $this->tagContainer = true;

        $this->tagText = $text;
        
        return $this;
    }

    
    public function count()
    {
        return array_reduce($this->tagChildren, function($lastResult, $child){
            return $lastResult + $child->count();
        }, 1);
    }

    protected function tagToString($level = 1)
    {
        $template = $this->getTemplate();

        $tag = $this->writeTagNameToTemplate($template);

        $attrs = $this->writeAttrToTemplate($tag);

        return $this->writeBodyToTemplate($attrs, $level);
    }

    private function getTemplate()
    {
        if ($this->tagContainer)
        {
            $template = $this->tagContOpen
                    . '[@BODY]'
                    . $this->tagContClose;
        } else
        {
            $template = $this->tagOpen . $this->tagClose;
        }

        return $template;
    }

    private function writeTagNameToTemplate($template)
    {

        $name = $this->tagName;

        $compose = array('[@TAG]' => $name);

        return $this->renderTemplate($template, $compose);
    }

    private function writeAttrToTemplate($template)
    {
        $attr = $this->tagAttr;

        $attrStr = $this->attributeToString($attr);

        $compose = array('[@ATTR]' => $attrStr ?  (' ' . $attrStr) : $attrStr);

        return $this->renderTemplate($template, $compose);
    }

    private function writeBodyToTemplate($template, $level)
    {
        $tagText = $this->tagText;

        count($this->tagChildren) and $tagText .= PHP_EOL;
        
        foreach ($this->tagChildren as $child) {
            $tagText .= str_repeat("\t", $level) . $child->tagToString($level + 1) . PHP_EOL;
        }

        count($this->tagChildren) and $tagText = $tagText . str_repeat("\t", $level - 1);
        
        $compose = array('[@BODY]' => $tagText);

        return $this->renderTemplate($template, $compose);
    }

    private function renderTemplate($template, $compose)
    {
        $html = $template;

        foreach ($compose as $search => $replace) {
            $html = str_replace($search, $replace, $html);
        }

        return $html;
    }
    
    public function render()
    {
        return $this->tagToString();
    }

    
    protected function removeDuplicateWords($attr)
    {
        return implode(' ', array_unique(explode(' ', $attr)));
    }
    
    /**
     * Attr to String
     *
     * Wraps the global attributes function and does some form specific work
     *
     * @param   array  $attr
     * @return  string
     */ 
    protected function attributeToString($attr) {

        $attr = array_filter($attr, function($value){
            return $value !== null;
        });
        
        foreach ($attr as $k => $v) 
        {
            $v = htmlentities($v);
            
            $attr[$k] = is_numeric($k) ? $v : "$k=\"$v\"";
        }
        
        return implode(' ', $attr);
    }
    
    public function children()
    {
        return $this->tagChildren;
    }
    
    public function __clone()
    {
        $tags = array();
        foreach ($this->tagChildren as $child) {
            $tags[] = clone($child);
        }
        $this->tagChildren = $tags;
    }

    public function __toString()
    {
        return $this->render();
    }
    
    public function __call($method, $args)
    {
        $name = strtolower(substr($method, 3));

        if (strpos($method, 'set') === 0)
        {
            return $this->setAttribute($name, $args[0]);
        }

        if (strpos($method, 'get') === 0)
        {
            return $this->getAttribute($name);
        }
    }
    
    public function flatten()
    {
        if(!count($this->tagChildren))
        {
            return array($this);
        }
        
        $flat = array();
        
        foreach($this->children() as $child)
        {
            foreach($child->flatten() as $flattened)
            {
                $flat[] = $flattened;
            }
        }
        
        return $flat;
    }
}
