<?php
/**
 * Fabryka odpowiedzialna za tworzenie klas.
 * Wykożystuje mapera ukrytego w podstawowym konfigu w celu zwiększenia modułowości w aplikacji.
 * @author Adam Nielski
 * @copyright Andryej Korbaś
 * @version 1.0
 * @package common
 * @subpackage class
 */
class Common_Class {
	private $_oConfig;
    static private $_oInstance;

    private function  __construct() {
        $this->_oConfig = Zend_Registry::get(REGISTRY_CONFIG);
    }

    static private function getInstance() {
        if (!self::$_oInstance instanceof Common_Class) {
            self::$_oInstance = new self();
        }
        return self::$_oInstance;
    }
    /**
     * Wydaje reflection klasy na podstawie jej nazwy.
     * Jesli nazwa klasy istnieje w maperze, przemapowują ją.
     * Refleksja deje możliwość wutworzenia innego obiektu klasy wraz z parametrami w konstruktorze
     * W przyszłości można obsługiwać np tagi opisujące klasy.
     * @param string $sClassName - nazwa klasy
     * @return ReflectionClass
     */
    static public function get( $sClassName ) {
        $oSelf = self::getInstance();
        return $oSelf->getReflection( $sClassName );
    }
    /**
     * @param string $sClassName - nazwa klasy
     * @return ReflectionClass
     */
    public function getReflection( $sClassName ) {
        if (is_object($this->_oConfig->class_maps)) {
            $sClassName = $this->_oConfig->class_maps->get($sClassName,$sClassName);
        }
        return new ReflectionClass( $sClassName );
    }
}