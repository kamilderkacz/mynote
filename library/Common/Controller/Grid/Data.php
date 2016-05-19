<?php
/* Klasa definiująca zmienne wejściowe z typowego grida ExtJS czy JQuery.
 * Udostępnia swoje dane poprzez metody dla Logiki budującej zapytania SQL.
 * @author Adam Nielski
 * @copyright Andryej Korbaś
 * @version 1.0
 * @package common
 * @subpackage controllers
 */
class Common_Controller_Grid_Data {

	/**
	 * Nazwa parametru zawierającego query z mechanizmów pagingu na stronach.
	 *
	 * @var string
	 */
	const QueryParam = 'query';

	/**
	 * Nazwa parametru określającego od którego elementu ma zostać wyświetlona zawrtość
	 * z mechanizmów pagingu na stronach.
	 *
	 * @var string
	 */
	const OffsetParam = 'start';

	/**
	 * Nazwa parametru określającego ile elementów ma zostać wyświetlonych na stronie
	 * z mechanizmów pagingu na stronach.
	 * Stosowane przede wszystkim przez Ext.data.Store.
	 *
	 * @var string
	 */
	const LimitParam = 'limit';

	/**
	 * Nazwa parametru określającego po jakim polu ma nastąpić sortowanie.
	 *
	 * @var string
	 */
	const SortParam = 'sort';

	/**
	 * Nazwa parametru określającego kierunek sortowania.
	 * Stosowane przede wszystkim przez Ext.data.Store.
	 *
	 * @var string
	 */
	const DirParam = 'dir';

	/**
	 * Nazwa parametru przechowującego tablicę identyfikatorów obiektów do usunięcia.
	 *
	 * @var string
	 */
	const DelDataParam = 'delData';
	/**
	 * Nazwa parametru przechowującego tablicę pól sortowania.
	 * Nie ustawiana w konstruktorze!
	 * @var string
	 */
    const MultiSort = 'multi';
    /**
     * Tablica przetrzymująca wszelkie dane wejściowe.
     * @var array
     */
	private $_aData;

    /**
     * Seter - ustawia zmienną where
     * @param string $sVar
     * @return self
     */
	public function setWhere( $sVar ) {
		$this->_aData[ self::QueryParam ] = $sVar;
		return $this;
	}
    /**
     * Seter - ustawia zmienną limit
     * @paraminteger $sVar
     * @return self
     */
    public function setLimit( $sVar ) {
		$this->_aData[ self::LimitParam ] = $sVar;
		return $this;
	}
    /**
     * Seter - ustawia zmienną offset - przesunięcie
     * @param integer $sVar
     * @return self
     */
	public function setOffset( $sVar ) {
		$this->_aData[ self::OffsetParam ] = $sVar;
		return $this;
	}
    /**
     * Seter - ustawia zmienną sort
     * @param string $sVar
     * @return self
     */
	public function setSort( $sVar ) {
		$this->_aData[ self::SortParam ] = $sVar;
		return $this;
	}
    /**
     * Seter - ustawia zmienną dir
     * @param string $sVar
     * @return self
     */
	public function setDir( $sVar ) {
		$this->_aData[ self::DirParam ] = $sVar;
		return $this;
	}
    /**
     * Seter - ustawia zmienną sort, która jest arrayem
     * @param array $sVar
     * @return self
     */
    public function setMultiSort( $sVar ) {
		$this->_aData[ self::MultiSort ] = $sVar;
		return $this;
    }
    /**
     * Pobiera wartość whera.
     * @return string
     */
	public function getWhere() {
		return (isset($this->_aData[ self::QueryParam ])?$this->_aData[ self::QueryParam ]:'');
	}
    /**
     * Pobiera wartość limit.
     * @return integer
     */
	public function getLimit() {
		return $this->_aData[ self::LimitParam ];
	}
    /**
     * Pobiera wartość offset.
     * @return integer
     */
	public function getOffset() {
		return (isset($this->_aData[ self::OffsetParam ])?$this->_aData[ self::OffsetParam ]:0);
	}
    /**
     * Pobiera wartość sort.
     * @return string
     */
	public function getSort() {
		return $this->_aData[ self::SortParam ];
	}
    /**
     * Pobiera wartość dir.
     * @return string
     */
	public function getDir() {
		return $this->_aData[ self::DirParam ];
	}
    /**
     * Pobiera wartość multi sorta.
     * @return array
     */
    public function getMultiSort() {
		return (isset($this->_aData[ self::MultiSort ])?$this->_aData[ self::MultiSort ]:null);
    }

}