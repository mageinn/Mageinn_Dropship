<?php
namespace Mageinn\Dropship\Model\ResourceModel\Batch;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Mageinn\Dropship\Model\ResourceModel\Batch
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
        $this->_init(\Mageinn\Dropship\Model\Batch::class, \Mageinn\Dropship\Model\ResourceModel\Batch::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
