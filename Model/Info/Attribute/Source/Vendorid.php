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
namespace Mageinn\Dropship\Model\Info\Attribute\Source;

/**
 * Class Vendorid
 * @package Mageinn\Dropship\Model\Info\Attribute\Source
 */
class Vendorid extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Mageinn\Dropship\Model\ResourceModel\Info\CollectionFactory
     */
    protected $infoFactory;

    /**
     * Vendorid constructor.
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Mageinn\Dropship\Model\ResourceModel\Info\CollectionFactory $infoFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Mageinn\Dropship\Model\ResourceModel\Info\CollectionFactory $infoFactory
    ) {
        $this->infoFactory = $infoFactory;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
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
     * @return \Mageinn\Dropship\Model\ResourceModel\Info\Collection
     */
    protected function _createVendorCollection()
    {
        return $this->infoFactory->create();
    }
}
