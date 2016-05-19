<?php
/** Klasa jest rodzicem dla klasy, która ma być dekoratorem jakiegoś obiektu.
 * Ten dekorator powinien posiadać metody, które są nadpisane bądź nie.
 * Głównym celem tej klasy jest skeszowanie wybranych metod, które podajemy np. tak:
 *	public function getNewestProductsView() {
 *		$this->setLifetime(3600);
 *		$this->setSerialize(false);
 *		$this->setTags(array(Common_Cache_Tag::TAG_PREFIX_HOME));
 *		return $this->addMethod('getNewestProductsView');
 *	}
 * Klasa sama w sobie nie powinna być tworzona jako obiekt.
 * @todo wykonac z niej abstrakcje.
 * @author Adam Nielski
 * @version 1.0
 * @package common
 * @subpackage decorator
 */
class Common_Decorator_CacheDecorator extends Common_Decorator_PseudoDecorator {
	/**
	 * Zmienna przchowujaca obiekt kesza
	 *
	 * @var Other_Cache_Cache
	 */
	protected $_oCache;
	
	/**
	 * Zmienna przechowująca obiekt typu dla kesza
	 *
	 * @var Other_Cache_Type
	 */
	protected $_oTag;
	
	/**
	 * @see Other_Decorator_PseudoDecorator::__construct
	 *
	 * @param mixed $oClass
	 */
	public function __construct($oClass) {
		$this->_oCache = new Common_Cache_Cache();
		$this->_oTag = new Common_Cache_Tag();
		parent::__construct($oClass);
	}
	
	/**
	 * Metoda ustawiająca czas trwania cache w aplikacji
	 *
	 * @param unknown_type $iLifetime
	 */
	public function setLifetime($iLifetime) {
		$this->_oCache->setLifetime($iLifetime);
	}
	
	/**
	 * Ustawienie statusu serializacji danych true/false
	 *
	 * @param bool $bSerialize
	 */
	public function setSerialize($bSerialize) {
		$this->_oCache->setSerialize($bSerialize);
	}
	
	/**
	 * Metoda ustawia tagi dla danego keszowania
	 *
	 * @param array $aTags
	 */
	public function setTags($aTags) {
		$this->_oTag->setTags($aTags);
	}
	
	/**
	 * Magiczna funkcja wywołująca metody bridge'a.
	 * Każde wywołanie metody klasy jest cache'owane.
	 * kluczem do cache'u jest domena aplikacji,
	 * nazwa metody oraz wszystkie parametry wywołania.
	 * 
	 * @param string $sName
	 * @param array $aArgs
	 * 
	 * @return mixed
	 */
	public function addMethod($sName, $aArgs = array()) {
		$sClassName = get_class($this->_oClass);
		if (method_exists($this->_oClass, $sName)) {
			$sId = $this->_oCache->getId($sClassName, $sName, $aArgs);
			$sResult = $this->_oCache->load($sId);
			if ($sResult != false) {
				return ($this->_oCache->getSerialize())? unserialize($sResult): $sResult;
			}	
			$sResult = $this->__call($sName, $aArgs);		
			$sNewResult = ($this->_oCache->getSerialize())? serialize($sResult): $sResult;
			$this->_oCache->save($sNewResult, $sId, $this->_oTag->getTags());
			return $sResult;
		}
		throw new Exception("Brak metody {$sName} w {$sClassName}.");
	}
	
	/**
	* metoda zwracająca obiekt cache.
	* 
	* @return Common_Cache_Cache
	*/
	public function getCache() {
		return $this->_oCache;
	}
}
