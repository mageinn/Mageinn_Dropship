<?php
namespace Mageinn\Dropship\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Region
 * @package Mageinn\Dropship\Model\ResourceModel
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
        $this->_init(\Mageinn\Dropship\Model\Region::REGIONS_TABLE, 'entity_id');
    }
}
