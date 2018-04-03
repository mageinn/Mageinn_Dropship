<?php
namespace Mageinn\Vendor\Model\ResourceModel\Info;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use \Mageinn\Vendor\Model\Info;
use \Mageinn\Vendor\Model\ResourceModel\Info as ResourceModelInfo;

/**
 * Class Collection
 *
 * @package Mageinn\Vendor\Model\ResourceModel\Info
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
        $this->_init(Info::class, ResourceModelInfo::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
