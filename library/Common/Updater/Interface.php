<?php
/**
 * Interface dla klas obsługujących wersjonowanie/aktualizowanie
 * mechanizmów bazy danych.
 * @author Adam Nielski
 * @copyright Agnieszka Korbaś
 * @version 1.0
 * @package common
 * @subpackage updater
*/
interface Common_Updater_Interface {
	/**
	 * Zwraca wersję, do której będzie aktualizowana baza.
	 * 
	 * @return string Łańcuch znaków w postaci "XX.YY.ZZ", gdzie "XX" oznacza
	 * główny numer wersji, "YY", numer wydania, a "ZZ" kolejny poprawkę/aktualizację,
	 * np. "1.12.3" - 3 poprawka/aktualizacja dla wersji 1.12
	 */
	public function getVersion();
	
	/**
	 * Uruchamia mechanizm aktualizacyjny.
	 */
	public function update();
}