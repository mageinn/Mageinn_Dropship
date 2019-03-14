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
namespace Mageinn\Dropship\Block\Adminhtml\Order\View;

/**
 * Class VendorInfo
 * @package Mageinn\Dropship\Block\Adminhtml\Order\View
 */
class VendorInfo extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    /**
     * @var int|null
     */
    protected $vendorId;

    /**
     * @var \Mageinn\Dropship\Model\Address
     */
    protected $vendor;

    /**
     * VendorInfo constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Mageinn\Dropship\Model\Address $vendor
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Mageinn\Dropship\Model\Address $vendor,
        array $data = []
    ) {
        $this->vendor = $vendor;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    /**
     * @param $vendorId
     */
    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;
    }

    /**
     * @return \Mageinn\Dropship\Model\Address
     */
    public function getVendor()
    {
        $addressType = \Mageinn\Dropship\Model\Address::ADDRESS_TYPE_CUSTOMER_SERVICE;
        $vendorAddressCollection = $this->vendor->getCollection()
            ->addFieldToFilter('vendor_id', ['eq' => "$this->vendorId"])
            ->addFieldToFilter('type', ['eq' => "$addressType"]);

        return $vendorAddressCollection->getFirstItem();
    }
}
