<?php
namespace Mageinn\Dropship\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ShippingRate
 * @package Mageinn\Dropship\Model\ResourceModel
 */
class ShippingRate extends AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageinn\Dropship\Model\ShippingRate::SHIPPING_RATES_TABLE, 'entity_id');
    }
}
