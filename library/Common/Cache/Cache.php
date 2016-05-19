<?php
/**
 * Klasa dla cache, ujednolicenie dostępnych metod bez względu na typ cache'owania
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage cache
 */
class Common_Cache_Cache {
	
	/*
	 * obiekt cache = mamcache lub file
	 */
	protected $_oCache;
	
	/*
	 * czas przetrzymywania cache
	 */
	protected $_iLifetime = 1800;
	
	/*
	 * zmienna przechowująca informacje o tym czy dane mają być serializowane
	 */
	protected $_bSerialize = true;
	
	protected $_memcacheEnable = true;
	
	/**
	 * Generuje klucz dla cache.
	 * 
	 * @param string $sMethodName
	 * @param array $aArgs
	 * 
	 * @return string
	 */
	public function getId($sClassName, $sMethodName, $aArgs) {
		foreach ( $aArgs as $sArgs ) {
			if (is_object ( $sArgs )) {
				if ($sArgs instanceof Common_Db_Row_Abstract) {
					$sArgs = $sArgs->id;
				} else {
					throw new Exception ( "Klasa nie jest instancją Common_Db_Row_Abstract" );
				}
			}
		}
		/**
		 * @todo Jeśli dla jednej domeny będa używane różne configi to klucz cache 
		 * tego nie obsłuży
		 */
		$sId = $_SERVER ['HTTP_HOST'] . '_' . $sClassName . '_' . $sMethodName . '_' . serialize ( $aArgs );
		//$sId = substr(Common_String::replaceSpecialChars($sId),0,221);
		$sId = Common_String::replaceSpecialChars ( $sId );
		if (strlen ( $sId ) > 221) {
			throw new Exception ( "Klucz wygenerowany dla metody {$sMethodName} jest dłuższy niż 221 znaków." );
		}
		return $sId;
	}
	
	/*
	 * ustawienie statusu serializacji danych true/false
	 */
	public function setSerialize($bSerialize) {
		$this->_bSerialize = ( bool ) $bSerialize;
	}
	
	/*
	 * pobieranie statusu serializacji
	 */
	public function getSerialize() {
		return $this->_bSerialize;
	}
	/*
	 * ustawienie czasu cache
	 */
	public function setLifetime($iLifetime) {
		$this->_iLifetime = ( int ) $iLifetime;
	}
	
	/*
	 * pobieranie czasu cache
	 */
	public function getLifetime() {
		return $this->_iLifetime;
	}
	
	/**
	 * Metoda która ustawia i zwraca cache w zależnoci od konfiguracji w  pliku config.ini
	 * @return  $this
	 */
	protected function setCache() {
		if (! $this->_oCache) {
            $this->_oConfig = new stdClass();
			$this->_oConfig->memcache->enable = false;
            $this->_oConfig->path->cache_dir = APPLICATION_PATH . "/../data/tmp";//Zend_Registry::get ( REGISTRY_CONFIG );
			if ($this->_oConfig->memcache->enable) {
				$this->_memcacheEnable = true;
				foreach ( new ArrayObject ( $this->_oConfig->memcache->toArray () ) as $srv ) {
					$aConfig = $srv;
				}
				$oCache = Zend_Cache::factory ( 'Core', 'Memcached', array ('lifetime' => $this->getLifetime (), 'automatic_serialization' => $this->getSerialize () ), array ('servers' => $aConfig ) );
			} else {
				$this->_memcacheEnable = false;
				$oCache = Zend_Cache::factory ( 'Core', 'File', array ('lifetime' => $this->getLifetime (), 'automatic_serialization' => $this->getSerialize () ), array ('cache_dir' => $this->_oConfig->path->cache_dir ) );
			}
			$this->_oCache = $oCache;
		}
		return $this;
	}
	
	/**
	 * Test if a cache is available for the given id and (if yes) return it (false else)
	 *
	 * @param  string  $id                     Cache id
	 * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
	 * @return string|false cached datas
	 */
	public function load($id, $doNotTestCacheValidity = false) {
		return $this->setCache()->_oCache->load ( $id, $doNotTestCacheValidity );
	}
	
	/**
	 * Test if a cache is available or not (for the given id)
	 *
	 * @param string $id cache id
	 * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
	 */
	public function test($id) {
		return $this->setCache ()->_oCache->test ( $id );
	}
	
	/**
	 * Save some string datas into a cache record
	 *
	 * Note : $data is always "string" (serialization is done by the
	 * core not by the backend)
	 *
	 * @param  string $data             Datas to cache
	 * @param  string $id               Cache id
	 * @param  array  $tags             Array of strings, the cache record will be tagged by each string entry
	 * @param  int    $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
	 * @return boolean True if no problem
	 */
	public function save($data, $id, $tags = array(), $specificLifetime = false, $memcacheTag=false) {
		if($this->_memcacheEnable && $memcacheTag != true) {
			$cache = new Common_Cache_Cache();
			$aCacheTags = unserialize($cache->load(Common_Cache_Tag::MEMCACHE_TAGS));
			foreach($tags as $sTag) {
				if(isset($aCacheTags[$sTag])) {
					$aTagId = $aCacheTags[$sTag];
				}
				$aTagId[$id] = $id;
				$aCacheTags[$sTag] = $aTagId;
			}
			$tags = array();
			$cache->save(serialize($aCacheTags), Common_Cache_Tag::MEMCACHE_TAGS, $tags, $specificLifetime, true);
		}
		return $this->setCache()->_oCache->save ( $data, $id, $tags, $specificLifetime );
	}
	
	/**
	 * Remove a cache record
	 *
	 * @param  string $id Cache id
	 * @return boolean True if no problem
	 */
	public function remove($id) {
		return $this->setCache ()->_oCache->remove ( $id );
	}
	
	/**
	 * Clean some cache records
	 *
	 * Available modes are :
	 * 'all' (default)  => remove all cache entries ($tags is not used)
	 * 'old'            => remove too old cache entries ($tags is not used)
	 * 'matchingTag'    => remove cache entries matching all given tags
	 *                     ($tags can be an array of strings or a single string)
	 * 'notMatchingTag' => remove cache entries not matching one of the given tags
	 *                     ($tags can be an array of strings or a single string)
	 *
	 * @param  string $mode Clean mode
	 * @param  array  $tags Array of tags
	 * @return boolean True if no problem
	 */
	public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array()) {
		if($this->_memcacheEnable && count($tags)>0) {
			$aCacheTags = unserialize($this->load(Common_Cache_Tag::MEMCACHE_TAGS));
			foreach($tags as $sTag) {
				if(isset($aCacheTags[$sTag])) {
					$aTagId = $aCacheTags[$sTag];
					foreach($aTagId as $sId) {
						$this->remove($sId);
					}
					unset($aCacheTags[$sTag]);
				}
			}
			$this->save(serialize($aCacheTags), Common_Cache_Tag::MEMCACHE_TAGS, array(), false, true);
			return true;
		}
		else {
			return $this->setCache ()->_oCache->clean ( $mode, $tags );
		}
	}
}