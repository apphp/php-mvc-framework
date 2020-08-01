<?php
/**
 * CShoppingCart provides a set of methods for common shopping cart operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:                  PROTECTED:                  PRIVATE:
 * ----------               ----------                  ----------
 * __construct              _insert
 * init (static)            _update
 * insert                   _saveCart
 * update
 * remove
 * destroy
 * total
 * totalItems
 * contents
 * getItem
 * itemExists
 * itemExistsByProductId
 * hasOptions
 * productOptions
 *
 */

class CShoppingCart extends CComponent
{

    /**
     * This is the regular expression rules that we use to validate the product ID
     * They are: alpha-numeric, dashes, underscores or periods
     *
     * @var string
     */
    protected $_productIdRules = '\.a-z0-9_-';
    /**
     * This is the regular expression rules that we use to validate the product name
     * alpha-numeric, dashes, underscores, colons or periods
     *
     * @var string
     */
    protected $_productNameRules = '\w \-\.\:';
    /**
     * Allow only safe product names
     *
     * @var bool
     */
    protected $_productNameSafe = true;
    /**
     * Allow UFT-8
     *
     * @var array
     */
    protected $_utf8Enabled = true;
    /**
     * @var array
     */
    protected $_cartContent = [];


    /**
     * Class default constructor
     */
    function __construct()
    {
        // Grab the shopping cart array from the session and initialize it
        $this->_cartContent = A::app()->getSession()->get('shopping_cart_content', null);
        if ($this->_cartContent === null) {
            $this->_cartContent = ['cart_total' => 0, 'total_items' => 0];
        }
    }

    /**
     * Returns the instance of object
     *
     * @return current class
     */
    public static function init()
    {
        return parent::init(__CLASS__);
    }

    /**
     * Insert items into the cart and save them
     * Ex.: $items = array(
     *         'id'      => 'SKU_123',
     *         'qty'     => 1,
     *         'price'   => 29.90,
     *         'name'    => 'T-Shirt',
     *         'image'   => 'images/product.png',
     *         'options' => array('Size' => 'L', 'Color' => 'Red')
     *      );
     *
     * @param  array  $items
     *
     * @return bool
     */
    public function insert($items = [])
    {
        // Check if any cart data was passed
        if ( ! is_array($items) || count($items) === 0) {
            return false;
        }

        // We can insert a single product using a one-dimensional array or multiple products using a multi-dimensional one.
        // The way we determine the array type is by looking for a required array key named "id" at the top level.
        // If it's not found, we will assume it's a multi-dimensional array.
        $saveCart = false;
        if (isset($items['id'])) {
            if (($rowId = $this->_insert($items))) {
                $saveCart = true;
            }
        } else {
            foreach ($items as $val) {
                if (is_array($val) && isset($val['id'])) {
                    if ($this->_insert($val)) {
                        $saveCart = true;
                    }
                }
            }
        }

        // Save the shopping cart data if insertion action was successful
        if ($saveCart === true) {
            $this->_saveCart();

            return isset($rowId) ? $rowId : true;
        }

        return false;
    }

    /**
     * Update the cart - permits the quantity of a given item to be changed
     * Ex.: $data = array(
     *         'rowid' => 'b99ccdf16028f015540f341130b6d8ec',
     *         'qty'   => 3
     *         ...
     *      );
     *
     * @param  array  $items
     *
     * @return bool
     */
    public function update($items = [])
    {
        // Is any data passed?
        if ( ! is_array($items) || count($items) === 0) {
            return false;
        }

        // You can either update a single product using a one-dimensional array, or multiple products using a multi-dimensional one.
        // The way we determine the array type is by looking for a required array key named "rowid".
        // If it's not found we assume it's a multi-dimensional array
        $saveCart = false;
        if (isset($items['rowid'])) {
            if ($this->_update($items) === true) {
                $saveCart = true;
            }
        } else {
            foreach ($items as $val) {
                if (is_array($val) && isset($val['rowid'])) {
                    if ($this->_update($val) === true) {
                        $saveCart = true;
                    }
                }
            }
        }

        // Save the cart data if the insert was successful
        if ($saveCart === true) {
            $this->_saveCart();

            return true;
        }

        return false;
    }

    /**
     * Remove Item - removes an item from the cart
     *
     * @param  int
     *
     * @return bool
     */
    public function remove($rowId)
    {
        // Unset & save
        unset($this->_cartContent[$rowId]);
        $this->_saveCart();

        return true;
    }


    /**
     * Destroy the cart
     * Empties the cart and kills the cart session variable
     *
     * @return void
     */
    public function destroy()
    {
        $this->_cartContent = ['cart_total' => 0, 'total_items' => 0];
        A::app()->getSession()->remove('shopping_cart_content');
    }

    /**
     * Cart Total returns count of items in cart
     *
     * @return int
     */
    public function total()
    {
        return $this->_cartContent['cart_total'];
    }

    /**
     * Total Items - returns the total item count
     *
     * @return int
     */
    public function totalItems()
    {
        return $this->_cartContent['total_items'];
    }

    /**
     * Cart Contents
     * Returns the entire cart array
     *
     * @param  bool
     *
     * @return array
     */
    public function contents($newestFirst = false)
    {
        // Do we want the newest item first?
        $cart = ($newestFirst) ? array_reverse($this->_cartContent) : $this->_cartContent;

        // Remove these so they don't create a problem when showing the cart table
        if (isset($cart['total_items'])) {
            unset($cart['total_items']);
        }
        if (isset($cart['cart_total'])) {
            unset($cart['cart_total']);
        }

        return $cart;
    }

    /**
     * Get cart item
     * Returns the details of a specific item in the cart
     *
     * @param  string  $rowId
     *
     * @return array
     */
    public function getItem($rowId)
    {
        return (in_array($rowId, ['total_items', 'cart_total'], true) || ! isset($this->_cartContent[$rowId]))
            ? false
            : $this->_cartContent[$rowId];
    }

    /**
     * Checks if cart item exists by row ID
     *
     * @param  string  $rowId
     *
     * @return bool
     */
    public function itemExists($rowId)
    {
        return (in_array($rowId, ['total_items', 'cart_total'], true) || ! isset($this->_cartContent[$rowId]))
            ? false
            : true;
    }

    /**
     * Checks if cart item exists
     *
     * @param  string  $productId
     * @param  int  $returnType  0 - bool, 1 - rowId
     *
     * @return bool|string
     */
    public function itemExistsByProductId($productId = null, $returnType = 0)
    {
        $return = false;

        foreach ($this->_cartContent as $key => $val) {
            // Check if product with such ID exists
            if ($val['id'] == $productId) {
                $return = ($returnType == 1) ? $key : true;
                break;
            }
        }

        return $return;
    }

    /**
     * Returns true value if the rowId that passed to this function correlates to an item that has options associated with it
     *
     * @param  string  $rowId
     *
     * @return bool
     */
    public function hasOptions($rowId = '')
    {
        return (isset($this->_cartContent[$rowId]['options']) && count($this->_cartContent[$rowId]['options']) !== 0);
    }

    /**
     * Product options
     * Returns the an array of options, for a particular product row ID
     *
     * @param  string  $rowId
     *
     * @return array
     */
    public function productOptions($rowId = '')
    {
        return isset($this->_cartContent[$rowId]['options']) ? $this->_cartContent[$rowId]['options'] : [];
    }

    /**
     * Insert single item into the cart and save them
     *
     * @param  array  $item
     *
     * @return bool
     */
    protected function _insert($item = [])
    {
        // Check if any cart data was passed
        if ( ! is_array($item) || count($item) === 0) {
            CDebug::addMessage(
                'errors',
                'cart_empty_data',
                A::t(
                    'core',
                    'The {method} method expects to be passed an array containing data.',
                    ['{method}' => 'CShoppingCart::insert()']
                )
            );

            return false;
        }

        // Does the $item array contain an id, quantity, price, and name? These are required parameters.
        if ( ! isset($item['id'], $item['qty'], $item['price'], $item['name'])) {
            CDebug::addMessage(
                'errors',
                'cart_missing_params',
                A::t('core', 'The cart array must contain a product ID, quantity, price, and name.')
            );

            return false;
        }

        // Prepare the quantity. It can only be a number and trim any leading zeros
        $item['qty'] = (float)$item['qty'];

        // If the quantity is zero or blank there's nothing for us to do
        if ($item['qty'] == 0) {
            return false;
        }

        // Validate the product ID. It can only be alpha-numeric, dashes, underscores or periods
        // Not totally sure we should impose this rule, but it seems prudent to standardize IDs.
        // Note: These can be user-specified by setting the $this->_productIdRules variable.
        if ( ! preg_match('/^['.$this->_productIdRules.']+$/i', $item['id'])) {
            CDebug::addMessage(
                'errors',
                'cart_wrong_product_id',
                A::t(
                    'core',
                    'Invalid product ID. The product ID can only contain alpha-numeric characters, dashes underscores and periods.'
                )
            );

            return false;
        }

        // Validate the product name. It can only be alpha-numeric, dashes, underscores, colons or periods.
        // Note: These can be user-specified by setting the $this->_productNameRules variable.
        if ($this->_productNameSafe
            && ! preg_match(
                '/^['.$this->_productNameRules.']+$/i'.($this->_utf8Enabled ? 'u' : ''),
                $item['name']
            )
        ) {
            CDebug::addMessage(
                'errors',
                'cart_wrong_product_name',
                A::t(
                    'core',
                    'An invalid name was submitted as the product name: {item}. The name can only contain alpha-numeric characters, dashes, underscores, colons and spaces.',
                    ['{item}' => $item['name']]
                )
            );

            return false;
        }

        // Prep the price. Remove leading zeros and anything that isn't a number or decimal point.
        $item['price'] = (float)$item['price'];

        // We now need to create a unique identifier for the item being inserted into the cart.
        // Every time something is added to the cart it is stored in the master cart array.
        // Each row in the cart array, however, must have a unique index that identifies not only
        // a particular product, but makes it possible to store identical products with different options.
        // If no options were submitted so we simply MD5 the product ID.
        if (isset($item['options']) && count($item['options']) > 0) {
            $rowId = md5($item['id'].serialize($item['options']));
        } else {
            $rowId = md5($item['id']);
        }

        // Now that we have our unique "row ID", we'll add our cart items to the master array
        // grab quantity if it's already there and add it on
        $oldQuantity = isset($this->_cartContent[$rowId]['qty']) ? (int)$this->_cartContent[$rowId]['qty'] : 0;

        // Re-create the entry, just to make sure our index contains only the data from this submission
        $item['rowid']              = $rowId;
        $item['qty']                += $oldQuantity;
        $this->_cartContent[$rowId] = $item;

        return $rowId;
    }

    /**
     * Update the cart - permits changing item properties
     *
     * @param  array  $items
     *
     * @return bool
     */
    protected function _update($items = [])
    {
        // Without these array indexes there is nothing we can do
        if ( ! isset($items['rowid'], $this->_cartContent[$items['rowid']])) {
            return false;
        }

        // Prepare the quantity
        if (isset($items['qty'])) {
            $items['qty'] = (float)$items['qty'];
            // Is the quantity zero - we remove the item from the cart
            // If the quantity is greater than zero - we are update quantity
            if ($items['qty'] == 0) {
                unset($this->_cartContent[$items['rowid']]);

                return true;
            }
        }

        // Find updatable keys
        $keys = array_intersect(array_keys($this->_cartContent[$items['rowid']]), array_keys($items));
        // If a price was passed, make sure it contains a valid data
        if (isset($items['price'])) {
            $items['price'] = (float)$items['price'];
        }

        // Product ID & name shouldn't be changed
        foreach (array_diff($keys, ['id', 'name']) as $key) {
            $this->_cartContent[$items['rowid']][$key] = $items[$key];
        }

        return true;
    }

    /**
     * Save the cart array to the session DB
     *
     * @return bool
     */
    protected function _saveCart()
    {
        // Add the individual prices and set the cart sub-total
        $this->_cartContent['total_items'] = $this->_cartContent['cart_total'] = 0;
        foreach ($this->_cartContent as $key => $val) {
            // We make sure the array contains the proper indexes
            if ( ! is_array($val) || ! isset($val['price'], $val['qty'])) {
                continue;
            }

            $this->_cartContent['cart_total']     += ($val['price'] * $val['qty']);
            $this->_cartContent['total_items']    += $val['qty'];
            $this->_cartContent[$key]['subtotal'] = ($this->_cartContent[$key]['price']
                * $this->_cartContent[$key]['qty']);
        }

        // If cart is empty - delete from the session cart content
        if (count($this->_cartContent) <= 2) {
            A::app()->getSession()->remove('shopping_cart_content');

            return false;
        }

        // Pass to the session cart content
        A::app()->getSession()->set('shopping_cart_content', $this->_cartContent);

        return true;
    }

}
