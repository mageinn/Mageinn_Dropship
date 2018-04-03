<?php
namespace Mageinn\Vendor\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class Info
 * @package Mageinn\Vendor\Model
 */
class BatchRow extends AbstractModel
{
    /**#@+
     * Table
     */
    const TABLE_DROPSHIP_BATCH_ROW = 'mageinn_dropship_batch_row';
    /**#@-*/

    /**
     * Object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageinn\Vendor\Model\ResourceModel\BatchRow::class);
    }
}
