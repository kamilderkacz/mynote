<?php
/* Klasa definiująca zmienne wejściowe z typowego grida ExtJS czy JQuery.
 * Udostępnia swoje dane poprzez metody dla Logiki budującej zapytania SQL.
 * @author Adam Nielski
 * @copyright Andryej Korbaś
 * @version 1.0
 * @package common
 * @subpackage controllers
 */
class Common_Controller_Grid_Data_Http extends Common_Controller_Grid_Data {

/**
 * Konstruktor ma za zadanie wyciągnięcie danych z requesta, który jest w kontrolerze a następnie wyodrębnienia
 * potrzebnych danych, zapisania ich do dalszego przetworzenia.
 * @todo można pokusić się o dostęp do requesta podobnie jak to robione jest w helperach.
 * @todo mulitisort nie jest na razie obsługiwany, istnieje ze względu już zbudowanej logiki pod wielokrotne sortowanie.
 * @param Common_Controller_NoView $oObj
 */
	public function __construct( Common_Controller_NoView $oObj ) {
		$this->setWhere( ( string ) $oObj->getParam( Common_Controller_Grid_Data::QueryParam, null ) )
			 ->setLimit( ( int ) $oObj->getParam( Common_Controller_Grid_Data::LimitParam, 100 ) )
			 ->setOffset( ( int ) ($oObj->getParam( Common_Controller_Grid_Data::OffsetParam, 0 ) -1) * $this->getLimit() )
			 ->setSort( ( string ) $oObj->getParam( Common_Controller_Grid_Data::SortParam, null ) )
			 ->setDir( ( string ) $oObj->getParam( Common_Controller_Grid_Data::DirParam, null ) )
             ->setMultiSort( array() );
	}
}