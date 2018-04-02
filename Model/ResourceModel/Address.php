<?php
namespace Mageinn\Dropship\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mageinn\Dropship\Model\Address as AddressModel;

/**
 * Class Address
 * @package Mageinn\Dropship\Model\ResourceModel
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
        $this->_init(AddressModel::VENDOR_ADDRESS_TABLE, 'entity_id');
    }
}
