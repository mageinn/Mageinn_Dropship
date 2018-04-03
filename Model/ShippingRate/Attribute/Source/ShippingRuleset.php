<?php

namespace Mageinn\Vendor\Model\ShippingRate\Attribute\Source;

use Mageinn\Vendor\Model\ShippingRate;

/**
 * Class ShippingRuleset
 *
 * @package Mageinn\Vendor\Model\ShippingRate\Attribute\Source
 */
class ShippingRuleset extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Mageinn\Vendor\Model\ResourceModel\ShippingRate\CollectionFactory $ratesFactory
     */
    protected $ratesFactory;

    /**
     * @var \Mageinn\Vendor\Model\Info\Attribute\Source\Vendorid
     */
    protected $vendorSource;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Mageinn\Vendor\Model\ResourceModel\ShippingRate\CollectionFactory $ratesFactory
     * @param \Mageinn\Vendor\Model\Info\Attribute\Source\Vendorid $vendorSource
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Framework\Registry $registry,
        \Mageinn\Vendor\Model\ResourceModel\ShippingRate\CollectionFactory $ratesFactory,
        \Mageinn\Vendor\Model\Info\Attribute\Source\Vendorid $vendorSource
    ) {
        $this->ratesFactory = $ratesFactory;
        $this->vendorSource = $vendorSource;
        $this->registry = $registry;

        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
     * Retrieve all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->getVendorOptions();
        }

        return $this->_options;
    }

    /**
     * @return \Mageinn\Vendor\Model\ResourceModel\ShippingRate\Collection
     */
    protected function _createShippingRulesetCollection($vendorId = false)
    {
        $collection = $this->ratesFactory->create()
            ->addFieldToSelect(ShippingRate::SHIPPING_RATE_DATA_GROUP);

        // Case: edit existing product.
        $product = $this->registry->registry('current_product');
        if (!$vendorId && $product) {
            $vendorId = $product->getVendorId();
        }

        // Case: create new product.
        if (!$vendorId) {
            $vendorSourceData = $this->vendorSource->getAllOptions();
            // Select the vendor from source in order to load the corresponding ruleset.
            $vendorId = empty($vendorSourceData) ? false : $vendorSourceData[0]['value'];
        }

        if ($vendorId) {
            $collection->addFieldToFilter(ShippingRate::SHIPPING_RATE_DATA_VENDOR_ID, (int)$vendorId);
        }

        // @codingStandardsIgnoreStart
        $collection->getSelect()->distinct(true);
        // @codingStandardsIgnoreEnd

        return $collection;
    }

    /**
     * @param bool $vendorId
     * @return array
     */
    public function getVendorOptions($vendorId = false)
    {
        $groups = $this->_createShippingRulesetCollection($vendorId)
            ->getColumnValues(ShippingRate::SHIPPING_RATE_DATA_GROUP);

        $options = [
            [
                'label' => 'None',
                'value' => null
            ]
        ];
        foreach ($groups as $group) {
            $options[] = [
                'label' => ucfirst($group),
                'value' => $group
            ];
        }

        return $options;
    }
}
