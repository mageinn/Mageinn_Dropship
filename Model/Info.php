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
use \Mageinn\Dropship\Model\ResourceModel\Info as ResourceModelInfo;

/**
 * Class Info
 * @package Mageinn\Dropship\Model
 */
class Info extends AbstractModel
{
    const VENDOR_INFO_TABLE = 'mageinn_dropship_information';

    const VENDOR_DATA_INFORMATION = 'vendor';

    const VENDOR_DATA_SETTINGS = 'settings';

    const VENDOR_BATCH_EXPORT_GENERAL = 'batch_export_general';

    const VENDOR_BATCH_IMPORT_GENERAL = 'batch_import_general';

    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 0;

    const FLAG_YES  = 1;
    const FLAG_NO   = 0;


    const CONFIGURATION_NOTIFICATION_EMAIL_TEMPLATE = 'dropship/notification/template';
    const CONFIGURATION_NOTIFICATION_SENDER = 'dropship/notification/sender';
    const CONFIGURATION_NOTIFICATION_RECIPIENT = 'dropship/notification/recipient';
    const CONFIGURATION_OPTION_DEFAULT_DROPSHIP_STATUS = 'dropship/shipment_status/default';
    const CONFIGURATION_OPTION_DEFAULT_DROPSHIP_ORDER_STATUS = 'dropship/shipment_status/make_available';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Info constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModelInfo::class);
    }

    /**
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * @return array
     */
    public function getAvailableFlags()
    {
        return [self::FLAG_YES => __('Yes'), self::FLAG_NO => __('No')];
    }

    /**
     * @return mixed
     */
    public function getVendorShipmentStatus()
    {
        return $this->scopeConfig->getValue(
            self::CONFIGURATION_OPTION_DEFAULT_DROPSHIP_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }
}
