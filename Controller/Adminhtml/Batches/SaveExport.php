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
namespace Mageinn\Dropship\Controller\Adminhtml\Batches;

/**
 * Class SaveExport
 * @package Mageinn\Dropship\Controller\Adminhtml\Batches
 */
class SaveExport extends \Magento\Backend\App\Action
{
    /**
     * @var \Mageinn\Dropship\Model\Batch
     */
    protected $_batch;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * @var \Mageinn\Dropship\Model\Info
     */
    protected $_vendor;

    /**
     * SaveExport constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mageinn\Dropship\Model\BatchFactory $batch
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Mageinn\Dropship\Model\InfoFactory $vendor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageinn\Dropship\Model\BatchFactory $batch,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Mageinn\Dropship\Model\InfoFactory $vendor
    ) {
        $this->_batch = $batch->create();
        $this->_dateTime = $dateTime;
        $this->_vendor = $vendor->create();
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $postData = $this->getRequest()->getPostValue();
        $batch = $postData['vendor_batches'];

        if (isset($batch['vendor_name'])) {
            $this->_vendor->load($batch['vendor_name']);
        } else {
            $this->messageManager->addError(__('Missing vendor data.'));
            return $resultRedirect->setPath('*/*/');
        }

        $time = strftime('%Y-%m-%d %H:%M:%S', $this->_dateTime->gmtTimestamp());
        try {
            $this->_batch
                ->setVendorId($batch['vendor_name'])
                ->setType(\Mageinn\Dropship\Model\Source\BatchType::MAGEINN_DROPSHIP_BATCH_TYPE_EXPORT)
                ->setStatus(\Mageinn\Dropship\Model\Source\BatchStatus::BATCH_STATUS_SCHEDULED)
                ->setCreatedAt($time)
                ->setScheduledAt($time)
                ->save();

            $this->messageManager->addSuccess(__('You saved the batch.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the batch.'));
        }
        return $resultRedirect->setPath('*/*/');
    }
}
