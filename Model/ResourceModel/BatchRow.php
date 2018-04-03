<?php
namespace Mageinn\Vendor\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class BatchRow
 * @package Mageinn\Vendor\Model\ResourceModel
 */
class BatchRow extends AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageinn\Vendor\Model\BatchRow::TABLE_DROPSHIP_BATCH_ROW, 'entity_id');
    }

    /**
     * Added to be able tu insert multiple instances without iterating through an array of data
     *
     * @param $data
     */
    public function bulkInsert($data)
    {
        if (is_array($data)) {
            $connection = $this->getConnection();
            try {
                $connection->beginTransaction();
                $connection->insertMultiple(
                    $this->getTable(\Mageinn\Vendor\Model\BatchRow::TABLE_DROPSHIP_BATCH_ROW),
                    $data
                );
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }
}
