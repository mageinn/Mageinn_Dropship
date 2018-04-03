<?php
namespace Mageinn\Vendor\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Address
 * @package Mageinn\Vendor\Model\ResourceModel
 */
class Address extends AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageinn_vendor_address', 'entity_id');
    }
}
