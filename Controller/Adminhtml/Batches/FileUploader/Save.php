<?php
namespace Mageinn\Dropship\Controller\Adminhtml\Batches\FileUploader;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * File Uploads Action Controller
 *
 * @package Mageinn\Dropship\Controller\Adminhtml\Batches\FileUploader
 */
class Save extends Action
{
    const FILE_UPLOAD_LOCATION_BATCH = 'vendor/';

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * Save constructor.
     * @param Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\File\UploaderFactory $fileUploaderFactory
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\UploaderFactory $fileUploaderFactory
    ) {
        $this->_filesystem = $filesystem
            ->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = null;
        try {
            $target = $this->_filesystem->getAbsolutePath(self::FILE_UPLOAD_LOCATION_BATCH);
            /** @var \Magento\Framework\File\Uploader $uploader */
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'vendor_batches[batch_file]']);
            $uploader->setAllowedExtensions(['csv']);
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($target);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}
