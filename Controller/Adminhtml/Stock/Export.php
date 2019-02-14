<?php

namespace Mageinn\Dropship\Controller\Adminhtml\Stock;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;
use Mageinn\Dropship\Helper\Stock;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\RawFactory;

/**
 * Class Export
 * @package Mageinn\Dropship\Controller\Adminhtml\Stock
 * @codeCoverageIgnore Controller functions don't need UT
 */
class Export extends Action
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var \Mageinn\Dropship\Helper\Stock
     */
    protected $stockHelper;
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var  /Mageinn\Dropship\Helper\Data
     */
    protected $vendorHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;


    /**
     * Export constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param Stock $stockHelper
     * @param FileFactory $fileFactory
     * @param RawFactory $rawFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Mageinn\Dropship\Helper\Data $vendorHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        Stock $stockHelper,
        FileFactory $fileFactory,
        RawFactory $rawFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Mageinn\Dropship\Helper\Data $vendorHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ) {
        $this->logger = $logger;
        $this->stockHelper = $stockHelper;
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $rawFactory;
        $this->directoryList = $directoryList;
        $this->vendorHelper = $vendorHelper;
        $this->date = $date;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Raw|
     * \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $vendorId = $this->getRequest()->getParam('vendor_id');
        $redirect = $this->resultRedirectFactory->create();
        if ($vendorId) {
            $varDirPath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
            $projectPath = $varDirPath .
                DIRECTORY_SEPARATOR .
                sprintf(Stock::FILE_SAVE_PATH, $vendorId);

            //create file name using vendor name, and date time
            $vendorName = $this->vendorHelper->getVendorNameById($vendorId) .
                '-stock-' . $this->date->date()->format('Y-m-d H:i') . '.csv';

            if ($this->stockHelper->exportStock($vendorId, $projectPath, $vendorName)) {
                try {
                    $this->fileFactory->create(
                        $vendorName,
                        [
                            'type' => 'filename',
                            'value' => $projectPath . $vendorName
                        ]
                    );
                    $resultRaw = $this->resultRawFactory->create();

                    return $resultRaw;
                } catch (\Exception $e) {
                    $this->logger->error($e);
                    $this->messageManager->addErrorMessage(__('Product stock file could not be downloaded'));

                    return $redirect->setPath('*/*/');
                }
            } else {
                $this->messageManager->addErrorMessage(__('Selected vendor has no products associated'));
                return $redirect->setPath('*/*/');
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select a vendor'));
        }
    }
}
