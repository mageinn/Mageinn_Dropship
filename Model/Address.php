<?php
/**
 * Mageinn
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageinn.com license that is
 * available through the world-wide-web at this URL:
 * https://mageinn.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */
namespace Mageinn\Dropship\Model;

use \Magento\Framework\Model\AbstractModel;
use \Mageinn\Dropship\Model\ResourceModel\Address as ResourceModelAddress;

/**
 * Class Address
 * @package Mageinn\Dropship\Model
 */
class Address extends AbstractModel
{

    const VENDOR_ADDRESS_TABLE = 'mageinn_dropship_address';

    const ADDRESS_TYPE_BILLING = 'billing_address';

    const ADDRESS_TYPE_SHIPPING = 'shipping_address';

    const ADDRESS_TYPE_CUSTOMER_SERVICE = 'customer_service';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModelAddress::class);
    }
}
