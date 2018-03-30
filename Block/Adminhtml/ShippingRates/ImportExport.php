<?php
namespace Mageinn\Dropship\Block\Adminhtml\ShippingRates;

use Mageinn\Dropship\Model\ResourceModel\Info\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;

/**
 * Class ImportExport
 * @package Mageinn\Dropship\Block\Adminhtml\ShippingRates
 */
class ImportExport extends Widget
{
    /**
     * @var CollectionFactory
     */
    protected $vendorsFactory;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->vendorsFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     *
     */
    public function getExportUrl()
    {
        return $this->getUrl('*/*/export');
    }

    /**
     * @return string
     *
     */
    public function getImportUrl()
    {
        return $this->getUrl('*/*/import');
    }

    /**
     * @return string
     *
     */
    public function getVendorsSelectHtml()
    {
        $vendors_collection = $this->vendorsFactory->create();

        $vendors_select = '<select name="vendor_id" class="admin__control-select shipping-rates-select required-entry">';
        foreach ($vendors_collection as $vendor)
            $vendors_select .= sprintf('<option value="%s">%s</option>', $vendor->getId(), $vendor->getName());

        $vendors_select .= '</select>';

        return $vendors_select;
    }
}
