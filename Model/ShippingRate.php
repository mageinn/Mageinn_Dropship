<?php
namespace Mageinn\Dropship\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class ShippingRate
 * @package Mageinn\Dropship\Model
 */
class ShippingRate extends AbstractModel
{
    /**#@+
     * Table
     */
    const SHIPPING_RATES_TABLE = 'mageinn_shipping_rates';
    /**#@-*/

    /**#@+
     * SHIPPING RATE Information Data
     */
    const SHIPPING_RATE_DATA_VENDOR_ID = 'vendor_id';
    const SHIPPING_RATE_DATA_COUNTRY = 'country';
    const SHIPPING_RATE_DATA_GROUP = 'shipping_group';
    const SHIPPING_RATE_DATA_DELIVERY = 'delivery_time';
    const SHIPPING_RATE_DATA_PRICE = 'price';

    /**#@-*/

    const SAVED_FILE_NAME = 'rates.csv';
    const FILE_SAVE_PATH = 'shippingRates/%s/';
    const IMPORT_FILE_TYPE = 'text/csv';

    /**
     * Object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageinn\Dropship\Model\ResourceModel\ShippingRate::class);
    }
}
