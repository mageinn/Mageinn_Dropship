<?php
/**
 * Mageinn
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageinn.com license that is
 * available through the world-wide-web at this URL:
 * https://mageinn.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */
namespace Mageinn\Dropship\Controller\Adminhtml\Vendor;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Registry;
use \Mageinn\Dropship\Model\Info;

/**
 * Class Delete
 * @package Mageinn\Dropship\Controller\Adminhtml\Vendor
 */
class Delete extends Action
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var Info
     */
    protected $vendorModel;

    /**
     * Delete constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Info $vendorModel
     */
    public function __construct(Context $context, Registry $coreRegistry, Info $vendorModel)
    {
        $this->coreRegistry = $coreRegistry;
        $this->vendorModel = $vendorModel;

        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id');

        if ($id) {
            try {
                $model = $this->vendorModel;
                $model->load($id)->delete();

                $this->messageManager->addSuccess(__('You deleted the vendor.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a vendor to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
