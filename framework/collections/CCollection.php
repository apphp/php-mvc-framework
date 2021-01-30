<?php
/**
 * CCollect provides a wrapper for working with arrays of data
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2021 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:                  PROTECTED:                  PRIVATE:
 * ----------               ----------                  ----------
 * __construct
 *
 * init (static)
 * all
 *
 */

class CCollection extends CComponent
{

    /**
     * The items contained in the collection
     * @var array
     */
    protected $_items = [];

    /**
     * Class default constructor
     */
    function __construct()
    {

    }

    /**
     * Returns the instance of object
     * @return current class
     */
    public static function init()
    {
        return parent::init(__CLASS__);
    }

    /**
     * Get all of the items in the collection
     *
     * @return array
     */
    public function all()
    {
        return $this->_items;
    }

}