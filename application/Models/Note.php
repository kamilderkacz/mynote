<?php

class Application_Model_Note
{
    //Parametry obiektu modelu
    protected $id;
    protected $authorId;
    protected $sectionId;
    protected $content;
    protected $title;
    protected $creationDatetime;
    protected $author;
    protected $removed;

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
    public function __set($name, $value) {
        $method = 'set' . $name;
        
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid note property (błąd widoku przy set)');
        }
        $this->$method($value);
    }
    // __get() and __set() will provide a convenience mechanism for us to access the individual entry properties, and proxy to the other getters and setters. They also will help ensure that only properties we whitelist will be available in the object. 
    public function __get($name) {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid note property (błąd widoku przy get)');
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
    public function setSectionId($id) {
        $this->sectionId = (int) $id;
        return $this;
    }
    public function getSectionId() {
        return $this->sectionId;
    }
    public function setAuthor($nick) {
        $this->author = (string) $nick;
        return $this;
    }
    public function getAuthor() {
        return $this->author;
    }
    public function setContent($text) {
        $this->content = (string)$text;
        return $this;
    }
    public function getContent() {
        return $this->content;
    }
    public function setTitle($text) {
        $this->title = (string)$text;
        return $this;
    }
    public function getTitle() {
        return $this->title;
    }
    public function setCreationDatetime($datetime) {
        $this->creationDatetime = (string) $datetime;
        return $this;
    }
    public function getCreationDatetime() {
        return $this->creationDatetime;
    }
    public function setRemoved($value) {
        $this->removed = (bool)$value;
        return $this;
    }
    public function getRemoved() {
        return $this->removed;
    }

    public function findNewer($note_id, $section_id) {

        $tNote = new Application_Model_DbTable_Note();
        
        $result = $tNote->fetchAll(
                array(
                    // 1st where statement
                    $tNote->select()
                    ->from($tNote, array(new Zend_Db_Expr('max(note_id) as maxId'))),
                    // 2nd where statement
                    'note_section_id = :note_section_id' // warunek konieczny!
                    ), 
                    // binding the values from 2nd where statement
                    array(':note_section_id' => $section_id)
                    
                )
                ->current(); // return row, not rowset
        
        $maxId = $result->maxId;

        // jeżeli $i jest równe lub większe od $maxId, pętla nie wykona się ani razu
        // jeżeli $i jest mniejsze niż $maxId
        for ($i = $note_id; $i != $maxId ;$i++) {
            //sprawdzam czy w ogóle taki id jest
            if ($tNote->find($i+1)) {
                return $i+1;
            } 
        }
        // THIS is the newest note
        return $note_id;
        
    }
    
    
}


