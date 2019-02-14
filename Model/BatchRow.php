<?php
namespace Mageinn\Dropship\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class Info
 * @package Mageinn\Dropship\Model
 */
class BatchRow extends AbstractModel
{
    /**#@+
     * Table
     */
    const TABLE_DROPSHIP_BATCH_ROW = 'iredeem_dropship_batch_row';
    /**#@-*/

    /**
     * Object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageinn\Dropship\Model\ResourceModel\BatchRow::class);
    }
}
