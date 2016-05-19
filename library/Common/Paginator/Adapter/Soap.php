<?php
/**
 * Created by PhpStorm.
 * User: Kamil
 * Date: 2015-02-16
 * Time: 13:21
 */

require_once 'Zend/Paginator/Adapter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Common_Paginator_Adapter_Soap implements Zend_Paginator_Adapter_Interface
{
    /**
     * Array
     *
     * @var array
     */
    protected $_array = null;

    /**
     * Item count
     *
     * @var integer
     */
    protected $_count = null;

    /**
     * Constructor.
     *
     * @param array $array Array to paginate
     */
    public function __construct($array)
    {
        $this->_array = isset($array->results) ? $array->results : array();
        $this->_count = isset($array->totalcount) ? $array->totalcount : 0;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->_array;
    }

    /**
     * Returns the total number of rows in the array.
     *
     * @return integer
     */
    public function count()
    {
        return $this->_count;
    }
}