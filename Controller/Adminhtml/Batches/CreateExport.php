<?php
namespace Mageinn\Dropship\Controller\Adminhtml\Batches;

/**
 * Class CreateImport
 * @package Mageinn\Dropship\Controller\Adminhtml\Batches
 */
class CreateExport extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPage;

    /**
     * CreateImport constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_resultPage = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPage->create();
        $resultPage->setActiveMenu('Iredeem_Vendor::vendor_batches')
            ->addBreadcrumb(__('Create Order Export Batch'), __('Create Order Export Batch'));
        $resultPage->getConfig()->getTitle()->prepend(__('Create Order Export Batch'));

        return $resultPage;
    }
}
