<?php
namespace Mageinn\Vendor\Controller\Adminhtml\Batches;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;

/**
 * Vendor Index Action
 * @package Mageinn\Vendor\Controller\Adminhtml\Vendor
 */
class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Load the defined page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageinn_Vendor::vendor_batches');
        $resultPage->addBreadcrumb(__('Batches'), __('Batches'));
        $resultPage->addBreadcrumb(__('Vendor Batches'), __('Vendor Batches'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import/Export Order Batches for Vendors'));

        return $resultPage;
    }
}
