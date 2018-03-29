<?php
namespace Mageinn\Dropship\Block\Adminhtml\Order\View;

class VendorInfo extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    protected $vendorId;

    /** @var \Mageinn\Dropship\Model\Info  */
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
     * Get vendor customer support address
     *
     * @return \Magento\Framework\DataObject
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
