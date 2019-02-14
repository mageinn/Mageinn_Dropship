<?php
namespace Iredeem\Vendor\Model\ResourceModel\Info;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use \Iredeem\Vendor\Model\Info;
use \Iredeem\Vendor\Model\ResourceModel\Info as ResourceModelInfo;

/**
 * Class Collection
 *
 * @package Iredeem\Vendor\Model\ResourceModel\Info
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
