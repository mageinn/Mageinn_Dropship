<?php
namespace Iredeem\Vendor\Controller\Adminhtml\Batches;

/**
 * Class ViewExport
 * @package Iredeem\Vendor\Controller\Adminhtml\Batches
 */
class ViewExport extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Iredeem\Vendor\Model\Batch
     */
    private $batchModel;

    /**
     * ViewExport constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Iredeem\Vendor\Model\Batch $batchModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultFactory,
        \Magento\Framework\Registry $registry,
        \Iredeem\Vendor\Model\Batch $batchModel
    ) {
        $this->resultFactory = $resultFactory;
        $this->_registry = $registry;
        $this->batchModel = $batchModel;

        parent::__construct($context);
    }

    /**
     * Customer edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->batchModel;

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This batch no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_registry->register('iredeem_batch', $model);
        $this->_registry->register('current_batch_type', 'Export');

        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Iredeem_Vendor::vendor_batches')->addBreadcrumb(__('Batch View'), __('Batch View'));
        $resultPage->getConfig()->getTitle()->prepend(__('View Export Orders Batch ' . $id));

        return $resultPage;
    }
}
