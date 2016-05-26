<?php

abstract class Element{
    
    private $name;
    private $attributes;
    
    function __construct($name){
        $this->name = $name;
        $this->$attributes = array();
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
          return $this->$attributes[$key];
        }
        return false;
    }
    
    function setAttribute($key,$value){
        $this->attributes[$key] = $value;
    }
    
    private function getAttributePairs(){
        $attributesString = '';
        foreach($this->attributes as $key => $value){
            $attributesString .= "$key=\"$value\" ";
        }
        return $attributeString;
    }

}

abstract class ElementFull extends Element{
    
    private $children = [];
    private $last = -1;
    
    function __construct($name){
        super($name);
    }
    
    function addChild($child){
        $this->children[] = $child;
        $this->last = count($this->children - 1);
        return $this->last;
    }
    
    function removeChild($index){
        if(isset($this->children[$index])){
            // remove reference to last before unsetting child
            if($this->last === $this->children[$index]){
                $this->last = -1;
                array_splice($this->children, $index, 1);
                // might be unecissary in the scope of top down form creation
                if(count($this->children)){
                    $this->last = count($this->children) - 1;   
                }
            }
            unset($this->children[$index]);
        }else{
            error_log("Could not remove child at index $index from object {$this->name}");
        }
    }

    function &last(){
        if($this->last == -1){
            throw new Exception("In object {$this->name} Last is equal to -1.");
        }else{
            return $this->children[$this->last];
        }
    }
    
    function __toString(){
        return sprintf("<%s %s></%s>",$this->name,$this->getAttributePairs(),$this->name);
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

abstract class ElementEmpty extends Element{
    
    function __construct($name){
        super($name);
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
        super("a");
        setAttribute("href", $href);
    }
    
}

class LineBreak extends ElementEmpty{
    
    function __construct(){
        super("br");
    }
    
}

class Span extends ElementFull{
    
    function __construct(){
        super("span");
    }
    
}

// in future add multi-line support

class Text{
    
    private $text;
    
    function __construct($text){
        $this->text = $text;
    }
    
    function __toString(){
        return $text;
    }
    
    function __sleep(){
        return $text;
    }
    
}

class ListUnordered extends ElementFull{
    function __construct(){
        super("ul");
    }
}

class ListOrdered extends ElementFull{
    function __construct(){
        super("ol");
    }
}

class Document extends Element{
    
    private $head;
    private $body;
    
    function __construct($title){
        super("html");
        $this->head = new Head();
        $this->body = new Body();
        $this->addChild=
    }
    
    function &head(){
        return $head;
    }
    
    function &body(){
        return $body;
    }
}

class Head extends ElementFull{
    
    function __construct(){
        super("head");
    }
    
}

class Body{
    
    function __construct(){
        super("body");
    }
}






