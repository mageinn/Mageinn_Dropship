<?php

namespace Mageinn\Dropship\Helper;

use Mageinn\Dropship\Model\Source\ShipmentStatus;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\App\Helper\Context;

/**
 * Class Data
 *
 * General helper for Vendor module
 *
 * @package Mageinn\Dropship\Helper
 */
class Data extends CoreData
{
    const XML_PATH_IREDEEM_VENDOR_BATCH_EXPORT = 'dropship/batch_order_export/';

    /** @var \Mageinn\Dropship\Model\Info  */
    protected $_vendor;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Mageinn\Dropship\Model\Info $vendor
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        \Mageinn\Dropship\Model\Info $vendor
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
