<?php

class Application_Model_Section
{
    //Parametry obiektu modelu
    protected $id;
    protected $authorId;
    protected $fullname;
    protected $color;
    protected $visibility;
    protected $removed;

    public function __construct(array $options =  null) {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid section property (błąd widoku przy set)');
        }
        $this->$method($value);
    }
    // __get() and __set() will provide a convenience mechanism for us to access the individual entry properties, and proxy to the other getters and setters. They also will help ensure that only properties we whitelist will be available in the object. 
    public function __get($name) {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid section property (błąd widoku przy get)');
        }
        return $this->$method();
    }

    public function setId($id) {
        $this->id = (int) $id;
        return $this;
    }
    public function getId() {
        return $this->id;
    }
    public function setAuthorId($id) {
        $this->authorId = (int) $id;
        return $this;
    }
    public function getAuthorId() {
        return $this->authorId;
    }
    public function setFullname($name) {
        $this->fullname = (string) $name;
        return $this;
    }
    public function getFullname() {
        return $this->fullname;
    }
    public function setColor($name) {
        $this->color = (string) $name;
        return $this;
    }
    public function getColor() {
        return $this->color;
    }
    public function setVisibility($value) {
        $this->visibility = (bool) $value;
        return $this;
    }
    public function getVisibility() {
        return $this->visibility;
    }
    public function setRemoved($value) {
        $this->removed = (bool) $value;
        return $this;
    }
    public function getRemoved() {
        return $this->removed;
    }
    
    
}


