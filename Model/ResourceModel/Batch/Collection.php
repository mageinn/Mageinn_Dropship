<?php
namespace Iredeem\Vendor\Model\ResourceModel\Batch;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Iredeem\Vendor\Model\ResourceModel\Batch
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Iredeem\Vendor\Model\Batch::class, \Iredeem\Vendor\Model\ResourceModel\Batch::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
