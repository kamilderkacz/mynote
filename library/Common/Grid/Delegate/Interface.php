<?php
/**
 * Definicja obiektu konfigurującego buildera.
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage grid
 */
interface Common_Grid_Delegate_Interface {
    /**
     * Zwraca tablicę konfigurującą podstawowe zapytanie grida.
     * @return array
     */
    public function getMainSelect();
    public function getSearchColumns();
}
