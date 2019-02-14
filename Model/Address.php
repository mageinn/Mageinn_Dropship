<?php
namespace Iredeem\Vendor\Model;

use \Magento\Framework\Model\AbstractModel;
use \Iredeem\Vendor\Model\ResourceModel\Address as ResourceModelAddress;

/**
 * Class Info
 * @package Iredeem\Vendor\Model
 */
class Address extends AbstractModel
{
    /**#@+
     * Table
     */
    const VENDOR_ADDRESS_TABLE = 'iredeem_vendor_address';
    /**#@-*/

    /**#@+
     * Address types
     */
    const ADDRESS_TYPE_BILLING = 'billing_address';

    const ADDRESS_TYPE_SHIPPING = 'shipping_address';

    const ADDRESS_TYPE_CUSTOMER_SERVICE = 'customer_service';
    /**#@-*/

    /**
     * Object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModelAddress::class);
    }
}
