<?php
namespace Mageinn\Vendor\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Region
 * @package Mageinn\Vendor\Model\ResourceModel
 */
class Region extends AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageinn\Vendor\Model\Region::REGIONS_TABLE, 'entity_id');
    }
}
