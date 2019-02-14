<?php
namespace Mageinn\Dropship\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Batch
 * @package Mageinn\Dropship\Model\ResourceModel
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
        $this->_init(\Mageinn\Dropship\Model\Batch::TABLE_DROPSHIP_BATCH, 'entity_id');
    }
}
