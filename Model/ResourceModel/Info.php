<?php
namespace Mageinn\Dropship\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Info
 * @package Mageinn\Dropship\Model\ResourceModel
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
        $this->_init('mageinn_dropship_information', 'entity_id');
    }
}
