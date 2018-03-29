<?php
namespace Mageinn\Dropship\Model\ResourceModel\ShippingRate;

use Mageinn\Dropship\Model\ShippingRate;
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Mageinn\Dropship\Model\ResourceModel\Region
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
        $this->_init(ShippingRate::class, \Mageinn\Dropship\Model\ResourceModel\ShippingRate::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
