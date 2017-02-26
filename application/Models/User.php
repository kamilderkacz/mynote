<?php

class Application_Model_User
{
    // Model's object params
    protected $id;
    protected $username;
    protected $password;
    protected $passwordSalt;
    protected $registerDatetime;
    protected $lastLoginDatetime;
    protected $email;
    protected $name;
    protected $lastName;
    protected $role;
    protected $active;

    public function __construct(array $options=NULL) {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    public function setOptions(array $options) { // Uruchamia settery na podst. tablicy przekazanej w konstruktorze
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value); // wywoływane funkcje
            }
        }
        return $this;
    }
    /*
     * __get() and __set() will provide a convenience mechanism for us to access
     * the individual entry properties, and proxy to the other getters and
     * setters. They also will help ensure that only properties we whitelist
     * will be available in the object. 
    */
    public function __set($name, $value) {
        $method = 'set' . $name;
        
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property (błąd widoku przy set)');
        }
        $this->$method($value);
    }
    public function __get($name) {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property (błąd widoku przy get)');
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
    public function setUsername($value) {
        $this->username = (string) $value;
        return $this;
    }
    public function getUsername() {
        return $this->username;
    }
    public function setPassword($value) {
        $this->password = (string) $value;
        return $this;
    }
    public function getPassword() {
        return $this->password;
    }
    public function setPasswordSalt($value) {
        $this->passwordSalt = (string) $value;
        return $this;
    }
    public function getPasswordSalt() {
        return $this->passwordSalt;
    }
    public function setRegisterDatetime($datetime) {
        $this->registerDatetime = (string) $datetime;
        return $this;
    }
    public function getRegisterDatetime() {
        return $this->registerDatetime;
    }
    public function setLastLoginDatetime($datetime) {
        $this->lastLoginDatetime = (string) $datetime;
        return $this;
    }
    public function getLastLoginDatetime() {
        return $this->lastLoginDatetime;
    }
    public function setEmail($value) {
        $this->email = (string) $value;
        return $this;
    }
    public function getEmail() {
        return $this->email;
    }
    public function setName($value) {
        $this->name = (string) $value;
        return $this;
    }
    public function getName() {
        return $this->name;
    }
    public function setLastName($value) {
        $this->lastName = (string) $value;
        return $this;
    }
    public function getLastName() {
        return $this->lastName;
    }
    public function setRole($value) {
        $this->role = (string) $value;
        return $this;
    }
    public function getRole() {
        return $this->role;
    }
    public function setActive($value) {
        $this->active = (bool)$value;
        return $this;
    }
    public function getActive() {
        return $this->active;
    }

}


