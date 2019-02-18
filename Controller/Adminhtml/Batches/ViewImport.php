<?php
namespace Mageinn\Dropship\Controller\Adminhtml\Batches;

/**
 * Class ViewImport
 * @package Mageinn\Dropship\Controller\Adminhtml\Batches
 */
class ViewImport extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Mageinn\Dropship\Model\Batch
     */
    private $batchModel;

    /**
     * ViewImport constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Mageinn\Dropship\Model\Batch $batchModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultFactory,
        \Magento\Framework\Registry $registry,
        \Mageinn\Dropship\Model\Batch $batchModel
    ) {
        $this->resultFactory = $resultFactory;
        $this->_registry = $registry;
        $this->batchModel = $batchModel;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
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

        $this->_registry->register('mageinn_batch', $model);
        $this->_registry->register('current_batch_type', 'Import');

        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Mageinn_Dropship::vendor_batches')->addBreadcrumb(__('Batch View'), __('Batch View'));
        $resultPage->getConfig()->getTitle()->prepend(__('View Import Orders Batch ' . $id));

        return $resultPage;
    }
}
