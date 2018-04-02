<?php
namespace Mageinn\Dropship\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mageinn\Dropship\Model\Info as InfoModel;
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
        $this->_init(InfoModel::VENDOR_INFO_TABLE, 'entity_id');
    }
}
