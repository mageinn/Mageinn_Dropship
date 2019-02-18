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
namespace Mageinn\Dropship\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class BatchRow
 * @package Mageinn\Dropship\Model\ResourceModel
 */
class BatchRow extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageinn\Dropship\Model\BatchRow::TABLE_DROPSHIP_BATCH_ROW, 'entity_id');
    }

    /**
     * @param $data
     */
    public function bulkInsert($data)
    {
        if (is_array($data)) {
            $connection = $this->getConnection();
            try {
                $connection->beginTransaction();
                $connection->insertMultiple(
                    $this->getTable(\Mageinn\Dropship\Model\BatchRow::TABLE_DROPSHIP_BATCH_ROW),
                    $data
                );
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }
}
