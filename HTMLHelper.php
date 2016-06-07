<?php

abstract class Element{
    
    protected $name;
    protected $attributes;
    public $depth = 0;
    
    function __construct($name){
        $this->name = $name;
        $this->attributes = array();
    }
    
    function attributes(){
        return $this->attributes;
    }
    
    function hasAttribute($key){
        if(isset($this->attributes['key'])){
            return true;
        }
        return false;
    }
    
    function getAttribute($key){
        if($this->hasAttribute($key)){
          return $this->attributes[$key];
        }
        return false;
    }
    
    function setAttribute($key,$value){
        $this->attributes[$key] = $value;
    }
    
    protected function getAttributePairs(){
        $attributesString = '';
        foreach($this->attributes as $key => $value){
            $attributesString .= "$key=\"$value\" ";
        }
        return $attributesString;
    }

}

class ElementFull extends Element{
    
    protected $children = [];
    protected $last = -1;

    function __construct($name){
        parent::__construct($name);
    }
    
    function addChild($child){
        $this->children[] = $child;
        $this->last = end($this->children);
        $this->last->depth = $this->depth + 1;
    }
    
    function removeChild($index){
        if(isset($this->children[$index])){
            unset($this->children[$index]);
            $this->last = end($this->children);
        }else{
            throw new Exception("There was no element at index $index");
        }
    }

    function &last(){
        return $this->last;
    }

    function getChildren(){
        $string = "";
        foreach($this->children as $child){
            $string .= $child;
        }
        return $string;
    }
    
    function __toString(){

        // indented by 2 spaces
        $s = str_repeat("\x20", $this->depth * 2);
        $n = $this->name;
        $a = $this->getAttributePairs();
        $c = $this->getChildren();

        if(count($this->children) === 1 && get_class(end($this->children)) == "Text"){
            return "$s<$n$a>$c</$n>";
        }else{
            return "$s<$n$a>$s\n$c\r$s</$n>\r";
        }
    }
    
    function __sleep(){
        
        $children = [];
        
        foreach($this->children as $child){
            $children[] = serialize($child);
        }
        
        return [
            "name" => $this->name,
            "attributes" => $this->attributes,
            "children" => $children,
            "closingtag" => true
        ];
    }
    
}

class ElementEmpty extends Element{
    
    function __construct($name){
        parent::__construct($name);
    }
    
    function __toString(){
        return sprintf("<%s %s />",$this->name,$this->getAttributePairs());
    }
    
    function __sleep(){
        return [
            "name" => $this->name,
            "attributes" => $this->attributes,
            "closingtag" => false
        ];
    }
    
}

/*
    Alphabetical element listing
*/

class Anchor extends ElementEmpty{
    
    function __construct($href){
        parent::__construct("a");
        setAttribute("href", $href);
    }
    
}

class LineBreak extends ElementEmpty{
    
    function __construct(){
        parent::__construct("br");
    }
    
}

class Span extends ElementFull{
    
    function __construct(){
        parent::__construct("span");
    }
    
}

// in future add multi-line support

class Text{
    
    private $text;
    public $depth = 0;
    
    function __construct($text){
        $this->text = $text;
    }
    
    function __toString(){
        return $this->text;
    }
    
    function __sleep(){
        return $this->text;
    }
    
}

class Header extends ElementFull{

    function __construct($level, $text)
    {
        assert($level >=1 && $level <= 6, "Invalid header level on line: " . __LINE__);
        parent::__construct("h$level");
        $this->addChild(new Text($text));
    }
}

class ListUnordered extends ElementFull{
    function __construct(){
        parent::__construct("ul");
    }
}

class ListOrdered extends ElementFull{
    function __construct(){
        parent::__construct("ol");
    }
}

/*
 * Explore options for referencing the head and body element for easier usage
 */
class Document extends ElementFull{
    
    private $head;
    private $body;
    
    function __construct($title){
        parent::__construct("html");
        $this->head = new Head();
        $this->body = new Body();

        $this->addChild($this->head);
        $this->addChild($this->body);
    }
    
    function &head(){
        return $this->head;
    }
    
    function &body(){
        return $this->body;
    }
}

class Head extends ElementFull{
    
    function __construct(){
        parent::__construct("head");
    }
    
}

class Body extends ElementFull{
    
    function __construct(){
        parent::__construct("body");
    }
}

class Meta extends ElementEmpty{

    function __construct($name, $content)
    {
        parent::__construct("meta");
        $this->setAttribute("name", $name);
        $this->setAttribute("content", $content);
    }

}






