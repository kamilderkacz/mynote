<?php
/**
 * Domyślny Bridge dla HTML
 * @author Adam Nielski
 * @copyright Agnieszka Korbaś
 * @version 1.0
 * @package common
 * @subpackage acl
 */
class Common_Acl_CacheDecorator_Resources extends Common_Decorator_CacheDecorator {
	
	public function __construct($oClass) {
		parent::__construct($oClass);
	}

	/**
	 * @see Common_Acl_Mgr_Resources::getAllByType()
	 */
	public function getAllByType($iResType) {
		$this->setLifetime(72000);
		$this->setSerialize(true);
		$this->setTags(array(Common_Cache_Tag::TAG_PREFIX_ACL));
		return $this->addMethod('getAllByType', array($iResType));
	}
	
	/**
	 * @see Common_Acl_Mgr_Resources::getByNameAndType()
	 */
	public function getByNameAndType($sName, $iResType) {
		$this->setLifetime(72000);
		$this->setSerialize(true);
		$this->setTags(array(Common_Cache_Tag::TAG_PREFIX_ACL));
		return $this->addMethod('getByNameAndType', array($sName, $iResType));
	}
}