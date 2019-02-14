<?php

namespace Mageinn\Dropship\Block\Adminhtml\Stock;

/**
 * Class ImportExport
 * @package Mageinn\Dropship\Block\Adminhtml\Stock
 */
class ImportExport extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Mageinn\Dropship\Model\ResourceModel\Info\CollectionFactory
     */
    protected $vendorsCollectionFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * ImportExport constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Mageinn\Dropship\Model\ResourceModel\Info\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Mageinn\Dropship\Model\ResourceModel\Info\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->authSession = $authSession;
        $this->vendorsCollectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getExportUrl()
    {
        return $this->getUrl('*/*/export');
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getImportUrl()
    {
        return $this->getUrl('*/*/import');
    }

    /** Get associated vendor for current admin user
     * @return string
     * @codeCoverageIgnore
     */
    public function getCurrentVendorsSelectHtml()
    {
        $admin = $this->authSession->getUser();
        $vendorsSelect = '<select name="vendor_id" class="admin__control-select product-stock-select required-entry">';
        $roleName = $admin->getRole()->getRoleName();
        $vendorsCollection = $this->vendorsCollectionFactory->create();

        foreach ($vendorsCollection as $vendor) {
            if ($roleName == 'Administrators') {
                $vendorsSelect .= '<option value="' . $vendor->getId() . '">' . $vendor->getName() . '</option>';
            } elseif (!empty($admin->getAssocVendorId())) {
                if (in_array($vendor->getId(), json_decode($admin->getAssocVendorId()))) {
                    $vendorsSelect .= '<option value="' . $vendor->getId() . '">' . $vendor->getName() . '</option>';
                }
            } else {
                $vendorsSelect .= '<option value="null">No vendor found</option>';
                $vendorsSelect .= '</select>';
                return $vendorsSelect;
            }
        }

        $vendorsSelect .= '</select>';
        return $vendorsSelect;
    }
}
