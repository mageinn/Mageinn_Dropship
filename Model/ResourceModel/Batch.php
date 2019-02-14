<?php
namespace Iredeem\Vendor\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Batch
 * @package Iredeem\Vendor\Model\ResourceModel
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
        $this->_init(\Iredeem\Vendor\Model\Batch::TABLE_DROPSHIP_BATCH, 'entity_id');
    }
}
