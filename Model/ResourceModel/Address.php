<?php
namespace Mageinn\Dropship\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

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
        $this->_init('mageinn_dropship_address', 'entity_id');
    }
}
