<?php
/* Dekorator cache'ujący wywołania metod klasy do memcache'a.
 * Ustawianie opcji
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage cache
 */
class Common_Cache_Tag {

    const MEMCACHE_TAGS = 'memcache_tags';
    /**
     * Sugerowany prefix do stosowania przy tworzeniu tagów dla strony glownej.
     */
    const TAG_PREFIX_HOME = 'strona_glowna';

    /**
     * Sugerowany prefix do stosowania przy tworzeniu tagów dla uprawnień.
     */
    const TAG_PREFIX_ACL = 'uprawnienia';

    private $_aTags;
    /**
     * Zamienia niedozwolone znaki w tagach.
     */
    protected function _parseTags($aTags) {
        if (is_string($aTags)) {
            $aTags = array($aTags);
        }
        $aNewTags = array();
        foreach ($aTags as $sTag) {
            $aNewTags[] = Common_String::replaceSpecialChars($sTag);
        }
        return $aNewTags;
    }

    /**
     * Ustawia tagi dla cache'u.
     * Wszystkie wywołania metod klasy będą tagowane tymi znacznikami.
     *
     * @param array $aTags
     */
    public function setTags($aTags) {
        $this->_aTags = $this->_parseTags($aTags);
    }

    /**
     * Pobiera tagi
     *
     * @return array
     */
    public function getTags() {
        return $this->_aTags;
    }
}