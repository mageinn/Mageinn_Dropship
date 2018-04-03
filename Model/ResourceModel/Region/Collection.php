<?php
namespace Mageinn\Vendor\Model\ResourceModel\Region;

use Mageinn\Vendor\Model\Region;
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Mageinn\Vendor\Model\ResourceModel\Region
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
        $this->_init(Region::class, \Mageinn\Vendor\Model\ResourceModel\Region::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
