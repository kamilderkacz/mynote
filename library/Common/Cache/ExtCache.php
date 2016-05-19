<?php
class Common_Cache_ExtCache extends Common_Cache_Cache {
	/**
	 * Zwraca zendowy obiekt cache do zastosowania we frameworku zend, np Zend_Paginaro::setCache
	 * 
	 * @return Zend_Cache
	 */
	public function getZendCache() {
		$this->setCache();
		return $this->_oCache;
	}	
}