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
namespace Mageinn\Dropship\Helper;

use Mageinn\Dropship\Model\Source\ShipmentStatus;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package Mageinn\Dropship\Helper
 */
class Data extends CoreData
{
    const XML_PATH_MAGEINN_DROPSHIP_BATCH_EXPORT = 'dropship/batch_order_export/';

    /** @var \Mageinn\Dropship\Model\Info  */
    protected $_vendor;

    /**
     * Data constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
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
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getBatchOrderExportConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_MAGEINN_DROPSHIP_BATCH_EXPORT . $code, $storeId);
    }

    /**
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
}
