<?php
namespace Mageinn\Dropship\Block\Adminhtml\Order\View;
use Mageinn\Dropship\Model\Address;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class VendorInfo
 * @package Mageinn\Dropship\Block\Adminhtml\Order\View
 */
class VendorInfo extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    protected $vendorId;

    /**
     * @var \Mageinn\Dropship\Model\Info
     */
    protected $vendor;

    /**
     * Constructor
     *
     * @param Context $context
     * @param StockConfigurationInterface $stockConfiguration
     * @param OptionFactory $optionFactory
     * @param StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Registry $registry
     * @param Address $vendor
     * @param array $data
     */
    public function __construct(
        Context $context,
        StockConfigurationInterface $stockConfiguration,
        OptionFactory $optionFactory,
        StockRegistryInterface $stockRegistry,
        \Magento\Framework\Registry $registry,
        Address $vendor,
        array $data = []
    ) {
        $this->vendor = $vendor;
        parent::__construct(
            $context,
            $stockRegistry,
            $stockConfiguration,
            $registry,
            $optionFactory,
            $data
        );
    }

    /**
     * @param int $vendorId
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
        $address_type = Address::ADDRESS_TYPE_CUSTOMER_SERVICE;

        $vendor_address_collection = $this->vendor->getCollection();
        $vendor_address_collection->addFieldToFilter('vendor_id', ['eq' => $this->vendorId]);
        $vendor_address_collection->addFieldToFilter('type', ['eq' => $address_type]);

        return $vendor_address_collection->getFirstItem();
    }
}
