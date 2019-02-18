<?php
/**
 * Mageinn
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageinn.com license that is
 * available through the world-wide-web at this URL:
 * https://mageinn.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */
namespace Mageinn\Dropship\Model\ResourceModel\Address;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use \Mageinn\Dropship\Model\Address;
use \Mageinn\Dropship\Model\ResourceModel\Address as ResourceModelAddress;

/**
 * Class Collection
 * @package Mageinn\Dropship\Model\ResourceModel\Address
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Address::class, ResourceModelAddress::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
