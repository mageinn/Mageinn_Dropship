<?php
namespace Iredeem\Vendor\Model\ResourceModel\BatchRow;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Iredeem\Vendor\Model\ResourceModel\BatchRow
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
        $this->_init(
            \Iredeem\Vendor\Model\BatchRow::class,
            \Iredeem\Vendor\Model\ResourceModel\BatchRow::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
