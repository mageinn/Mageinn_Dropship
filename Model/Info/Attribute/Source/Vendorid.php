<?php
/**
 * Catalog product vendor attribute source
 */
namespace Iredeem\Vendor\Model\Info\Attribute\Source;

/**
 * Class Collection
 *
 * @package Iredeem\Vendor\Model\ResourceModel\Info
 */
class Vendorid extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Iredeem\Vendor\Model\ResourceModel\Info\CollectionFactory
     */
    protected $infoFactory;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Iredeem\Vendor\Model\ResourceModel\Info\CollectionFactory $infoFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Iredeem\Vendor\Model\ResourceModel\Info\CollectionFactory $infoFactory
    ) {
        $this->infoFactory = $infoFactory;
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
            $options = $this->_createVendorCollection()->toOptionArray();
            usort($options, function ($a, $b) {
                return strcmp(strtolower($a["label"]), strtolower($b["label"]));
            });
            $this->_options = $options;
        }
        return $this->_options;
    }

    /**
     * @return \Iredeem\Vendor\Model\ResourceModel\Info\Collection
     */
    protected function _createVendorCollection()
    {
        return $this->infoFactory->create();
    }
}
