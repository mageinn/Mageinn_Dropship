<?php

namespace Mageinn\Vendor\Helper;

use Mageinn\Vendor\Model\Source\ShipmentStatus;
use Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Store\Model\ScopeInterface;


/**
 * Class Data
 *
 * General helper for Vendor module
 *
 * @package Mageinn\Vendor\Helper
 */
class Data extends AbstractHelper
{
    const XML_PATH_MAGEINN_VENDOR_BATCH_EXPORT = 'dropship/batch_order_export/';

    /**
     * @var \Mageinn\Vendor\Model\Info
     */
    protected $_vendor;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Mageinn\Vendor\Model\Info $vendor
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        \Mageinn\Vendor\Model\Info $vendor
    ) {
        $this->_vendor = $vendor;
        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Get batch order export general configs
     *
     * @param string $code
     * @param null   $storeId
     * @return mixed
     */
    public function getBatchOrderExportConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_MAGEINN_VENDOR_BATCH_EXPORT . $code, $storeId);
    }

    /**
     * Get vendor name by id
     *
     * @param $id
     * @return mixed
     */
    public function getVendorNameById($id)
    {
        return $this->_vendor->load($id)->getName();
    }

    /**
     * @param $value
     * @return string
     */
    public function getShipmentStatusLabel($value)
    {
        return ShipmentStatus::getLabel($value);
    }

    /**
     * General method for getting a config value.
     *
     * Will be called for the specific configs in the future modules
     *
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Insert multiple values
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param $data
     * @throws LocalizedException
     */
    public function bulkInsert(\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource, $data)
    {
        $connection = $resource->getConnection();
        try {
            $connection->beginTransaction();
            $connection->insertMultiple($resource->getMainTable(), $data);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Update multiple values
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param array $data
     * @param array $fields
     * @throws LocalizedException
     */
    public function bulkUpdate(\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource, $data, $fields = [])
    {
        $connection = $resource->getConnection();
        try {
            $connection->beginTransaction();
            $connection->insertOnDuplicate($resource->getMainTable(), $data, $fields);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param $where
     * @param $data
     * @throws LocalizedException
     */
    public function bulkDeleteAndInsert(\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource, $where, $data)
    {
        $connection = $resource->getConnection();
        try {
            $connection->beginTransaction();
            $connection->delete($resource->getMainTable(), $where);
            $connection->insertMultiple($resource->getMainTable(), $data);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
