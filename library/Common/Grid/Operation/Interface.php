<?php
/**
 * Definicja metod operacji dla akcji kontrolera obslugujacego grida.
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage grid
 */
interface Common_Grid_Operation_Interface {
    /**
     *
     * @param array $aParams - tablica parametrów wejściowych (z requesta)
     * @return Common_Db_ListResult - lista danych
     */
    public function getList( array $aParams );
}
