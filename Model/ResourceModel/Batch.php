<?php
namespace Mageinn\Vendor\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Batch
 * @package Mageinn\Vendor\Model\ResourceModel
 */
class Batch extends AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageinn\Vendor\Model\Batch::TABLE_DROPSHIP_BATCH, 'entity_id');
    }
}
