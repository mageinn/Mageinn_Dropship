<?php
namespace Mageinn\Dropship\Controller\Adminhtml\Vendor;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\View\Result\PageFactory;
use \Mageinn\Dropship\Model\Info;

class Edit extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var Info
     */
    protected $vendorModel;

    /**
     * Edit action constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Info $vendorModel
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        Info $vendorModel
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->vendorModel = $vendorModel;

        parent::__construct($context);
    }

    /**
     * Customer edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->vendorModel;

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This vendor no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('mageinn_dropship', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageinn_Dropship::vendor')->addBreadcrumb(
            $id ? __('Edit Vendor' . $model->getVendorName()) : __('New Vendor'),
            $id ? __('Edit Vendor' . $model->getVendorName()) : __('New Vendor')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Vendors'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New Vendor'));

        return $resultPage;
    }
}
