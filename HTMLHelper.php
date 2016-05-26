<?php

// require cbor

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
                $this->last = count($this->children);
            }
            unset($this->children[$index]);
        }else{
            error_log("Could not remove child at index $index from object $name");
        }
    }

    function &last(){
        return $this->children[$this->last];
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


