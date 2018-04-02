<?php
namespace Mageinn\Dropship\Controller\Adminhtml\Vendor;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Mageinn\Dropship\Controller\Adminhtml\Vendor
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Load the defined page.
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $result_page */
        $result_page = $this->resultPageFactory->create();
        $result_page->setActiveMenu('Mageinn_Dropship::vendor_vendors');
        $result_page->addBreadcrumb(__('Sales'), __('Sales'));
        $result_page->addBreadcrumb(__('Dropships'), __('Dropships'));
        $result_page->getConfig()->getTitle()->prepend(__('Dropships'));

        return $result_page;
    }
}
