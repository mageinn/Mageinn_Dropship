<?php
namespace Mageinn\Vendor\Block\Adminhtml\ShippingRates;

/**
 * Class ImportExport
 * @package Mageinn\Vendor\Block\Adminhtml\ShippingRates
 */
class ImportExport extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Mageinn\Vendor\Model\ResourceModel\Info\CollectionFactory
     */
    protected $vendorsCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Mageinn\Vendor\Model\ResourceModel\Info\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->vendorsCollectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getExportUrl()
    {
        return $this->getUrl('*/*/export');
    }

    /**
     * @return string
     */
    public function getImportUrl()
    {
        return $this->getUrl('*/*/import');
    }

    /**
     * @return string
     */
    public function getVendorsSelectHtml()
    {
        $vendorsCollection = $this->vendorsCollectionFactory->create();

        $vendorsSelect = '<select name="vendor_id" class="admin__control-select shipping-rates-select required-entry">';
        foreach ($vendorsCollection as $vendor) {
            $vendorsSelect .= '<option value="' . $vendor->getId() . '">' . $vendor->getName() . '</option>';
        }
        $vendorsSelect .= '</select>';

        return $vendorsSelect;
    }
}
