<?php 
/**
 * Klasa paginatora wymuszająca działanie bez cachowania. 
 * Niestety domyslnie gdy włączy sie cachowanie w paginatorze to dotyczy wszystkich pagonatorów i tych ktore chcemy cachowac i 
 * tych ktore powinny isc prosto z bazy... np koszyk zakupionych towarów
 */
class Common_Paginator_NoCache extends Zend_Paginator {
	public function __construct($adapter) {
		parent::__construct($adapter);
		$this->setCacheEnabled(false);
	}
}