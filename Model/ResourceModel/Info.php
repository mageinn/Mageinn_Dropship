<?php
namespace Iredeem\Vendor\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Info
 * @package Iredeem\Vendor\Model\ResourceModel
 */
class Info extends AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('iredeem_vendor_information', 'entity_id');
    }
}
