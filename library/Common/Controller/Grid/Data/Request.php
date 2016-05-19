<?php
/* Klasa definiująca zmienne wejściowe z typowego grida ExtJS czy JQuery.
 * Udostępnia swoje dane poprzez metody dla Logiki budującej zapytania SQL.
 * @author Adam Nielski
 * @copyright Andryej Korbaś
 * @version 1.0
 * @package common
 * @subpackage controllers
 */
class Common_Controller_Grid_Data_Request extends Common_Controller_Grid_Data {

/**
 * Konstruktor ma za zadanie wyciągnięcie danych z requesta, który jest w kontrolerze a następnie wyodrębnienia
 * potrzebnych danych, zapisania ich do dalszego przetworzenia.
 * @todo można pokusić się o dostęp do requesta podobnie jak to robione jest w helperach.
 * @todo mulitisort nie jest na razie obsługiwany, istnieje ze względu już zbudowanej logiki pod wielokrotne sortowanie.
 * @param array $aObj - dane z requesta
 */
	public function __construct( array $aObj ) {
        isset($aObj[Common_Controller_Grid_Data::QueryParam])?$this->setWhere( ( string ) $aObj[Common_Controller_Grid_Data::QueryParam] ): $this->setWhere( null );
        isset($aObj[Common_Controller_Grid_Data::LimitParam])?$this->setLimit( ( int ) $aObj[Common_Controller_Grid_Data::LimitParam] ): $this->setLimit( 100 );//@todo 100 dać do stałych
        isset($aObj[Common_Controller_Grid_Data::OffsetParam])?$this->setOffset( ( int ) ($aObj[Common_Controller_Grid_Data::OffsetParam] )  ): $this->setOffset( 0 );
        isset($aObj[Common_Controller_Grid_Data::SortParam])?$this->setSort( ( string ) $aObj[Common_Controller_Grid_Data::SortParam] ): $this->setSort( null );
        isset($aObj[Common_Controller_Grid_Data::DirParam])?$this->setDir( ( string ) $aObj[Common_Controller_Grid_Data::DirParam] ): $this->setDir( null );
        $this->setMultiSort( array() );
	}
}