<?php

namespace Iredeem\Vendor\Helper;

use Iredeem\Vendor\Model\Source\ShipmentStatus;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\App\Helper\Context;

/**
 * Class Data
 *
 * General helper for Vendor module
 *
 * @package Iredeem\Vendor\Helper
 */
class Data extends CoreData
{
    const XML_PATH_IREDEEM_VENDOR_BATCH_EXPORT = 'dropship/batch_order_export/';

    /** @var \Iredeem\Vendor\Model\Info  */
    protected $_vendor;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Iredeem\Vendor\Model\Info $vendor
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        \Iredeem\Vendor\Model\Info $vendor
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
        return $this->getConfigValue(self::XML_PATH_IREDEEM_VENDOR_BATCH_EXPORT . $code, $storeId);
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

    public function getShipmentStatusLabel($value)
    {
        return ShipmentStatus::getLabel($value);
    }
}
