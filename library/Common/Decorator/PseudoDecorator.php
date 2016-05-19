<?php
/* Klasa jest dekoratorem każdego obiektu. Jej działanie jest wręcz przeźroczyste.
 * Wystarczy po niej podziedziczyć i nadpisać zachowania obiektu którego chcemy udekorować.
 * Kalsa nie jest typowym dekoratorem - potrafi całkowicie zmienić funkcjonalność obiektu.
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage decorator
 */
abstract class Common_Decorator_PseudoDecorator {
	
	/**
	 * Instancja klasy = obiekt, który dekorujemy.
	 * 
	 * @var Main_Class_aAbstract
	 */
	protected $_oClass;
	
	/**
	 * Domyślny konstruktor.
	 * 
	 * @param Object $oClass - obiekt któy chcemy udekorować
	 */
	public function __construct($oClass) {
		$this->_oClass = $oClass;
	}
	
	/**
	 * Przezroczysta metoda przez którą przechodzą metody
	 *
	 * @param string $sName
	 * @param array $aArgs
	 * @return mixed zwraca to co zwróci wywołana metoda
	 */
	public function __call($sName, $aArgs) {
		$sResult = call_user_func_array ( array ($this->_oClass, $sName ), $aArgs );
		return $sResult;
	}
	
	/**
	 * Przezroczysta metoda przez którą przechodzi ustawianie publicznych zmiennych
	 *
	 * @param string $sName
	 * @param mixed $mValue
	 */
	public function __set($sName, $mValue) {
		$this->_oClass->{$sName} = $mValue;
	}
	
	/**
	 * Przezroczysta metoda która zwraca wartośći publicznych zmiennych
	 *
	 * @param string $sName
	 * @return mixed zwraca wartość danej zmiennej jeśli istnieje
	 */
	public function __get($sName) {
		$this->_oClass->{$sName};
	}
	
	/**
	 * Przezroczysta metoda sprawdzający czy istnieje taka zmienna dla danej klasy
	 *
	 * @param string $sName
	 * @return bool
	 */
	public function __isset($sName) {
		return isset ( $this->_oClass->{$sName} );
	}
	/**
	 * Przezroczysta metoda usuwająca wartość z danej zmiennej
	 *
	 * @param string $sName
	 */
	public function __unset($sName) {
		unset ( $this->_oClass->{$sName} );
	}
}
