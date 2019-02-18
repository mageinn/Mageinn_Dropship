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
namespace Mageinn\Dropship\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class BatchRow
 * @package Mageinn\Dropship\Model
 */
class BatchRow extends AbstractModel
{
    const TABLE_DROPSHIP_BATCH_ROW = 'mageinn_dropship_batch_row';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageinn\Dropship\Model\ResourceModel\BatchRow::class);
    }
}
