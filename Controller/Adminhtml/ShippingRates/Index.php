<?php
namespace Mageinn\Dropship\Controller\Adminhtml\ShippingRates;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;

/**
 * Dropship Index Action
 * @package Mageinn\Dropship\Controller\Adminhtml\Dropship
 */
class Index extends Action
{
    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultFactory = $resultPageFactory;
    }

    /**
     * Load the defined page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create();
        $resultPage->setActiveMenu('Mageinn_Dropship::vendor_shipping_rates');
        $resultPage->addBreadcrumb(__('Shipping Rates'), __('Shipping Rates'));
        $resultPage->addBreadcrumb(__('Import/Export'), __('Import/Export'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import/Export Shipping Rates'));

        return $resultPage;
    }
}
