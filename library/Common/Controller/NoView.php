<?php
/* Klasa bazowa dla kontrollerów aplikacji BAC nie posiadających widoków - inicjalizuje globalne zmienne.
 * Posiada wsparcie dla JSON'a używając helpera.
 * Ostatnio wspiera także inne wyjścia - co jest do przeudowy
 * @author Adam Nielski
 * @copyright Andryej Korbaś
 * @version 1.0
 * @package common
 * @subpackage controllers
 */
class Common_Controller_NoView extends Common_Controller_View {
    /**
     * Inicjalizacja obiektu - wyłączenie sprawdzania widoku (pliku phtml).
     * Dodanie katalogu klas dodatkowych helperów dla akcji.
     */
	public function init() {
		$oFrontCtrl = $this->getFrontController();
		$oFrontCtrl->setParams(array('noViewRenderer' => true, 'neverRender' => true));
		parent::init();
	}
    
    protected function _setViewParam() {
        ;
    }
    
    /**
     * Upublicznienie metody chronionej do celów dlaszego przetwarzania obiektu przez inne obiekty.
     * @param string $paramName - nazwa prametru
     * @param variant $default - domyslna wartosc w przypadku braku parametru
     * @return variant
     */
	public function getParam($paramName, $default = null) {
		return $this->_getParam($paramName, $default = null);
	}
}