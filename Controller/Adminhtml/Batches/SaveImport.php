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
 * Class SaveImport
 * @package Mageinn\Dropship\Controller\Adminhtml\Batches
 */
class SaveImport extends \Magento\Backend\App\Action
{
    /**
     * @var \Mageinn\Dropship\Model\Batch
     */
    protected $_batch;

    /**
     * @var \Mageinn\Dropship\Model\Info
     */
    protected $_vendor;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * SaveImport constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mageinn\Dropship\Model\BatchFactory $batch
     * @param \Mageinn\Dropship\Model\InfoFactory $vendor
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageinn\Dropship\Model\BatchFactory $batch,
        \Mageinn\Dropship\Model\InfoFactory $vendor,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->_batch = $batch->create();
        $this->_vendor = $vendor->create();
        $this->_filesystem = $filesystem
            ->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        $this->_dateTime = $dateTime;
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

        $file = $this->_vendor->getBatchImportSource();
        if (isset($batch['default_file_location'])
            && !$batch['default_file_location']
            && isset($batch['batch_file'])
            && $batch['batch_file']
        ) {
            $fileData = reset($batch['batch_file']);
            $file = $this->_filesystem->getRelativePath($fileData['path'] . $fileData['file']);
        }

        try {
            $this->_batch
                ->setVendorId($batch['vendor_name'])
                ->setType(\Mageinn\Dropship\Model\Source\BatchType::MAGEINN_DROPSHIP_BATCH_TYPE_IMPORT)
                ->setStatus(\Mageinn\Dropship\Model\Source\BatchStatus::BATCH_STATUS_SCHEDULED)
                ->setCreatedAt($time)
                ->setScheduledAt($time)
                ->setFilePath($file)
                ->save();

            $this->messageManager->addSuccess(__('You saved the batch.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the batch.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
