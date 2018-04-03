<?php
namespace Mageinn\Vendor\Controller\Adminhtml\ShippingRates;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\App\Response\Http\FileFactory;
use \Magento\Framework\Controller\Result\RawFactory;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Mageinn\Vendor\Model\ShippingRate;

/**
 * Vendor Index Action
 * @package Mageinn\Vendor\Controller\Adminhtml\Vendor
 */
class Export extends Action
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Export constructor.
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param RawFactory $rawFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        RawFactory $rawFactory,
        LoggerInterface $logger
    ) {
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $rawFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Load the defined page
     */
    public function execute()
    {
        $vendorId = $this->getRequest()->getParam('vendor_id');
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('*/*/');

        if ($vendorId) {
            $projectPath = DirectoryList::VAR_DIR .
                DIRECTORY_SEPARATOR .
                sprintf(ShippingRate::FILE_SAVE_PATH, $vendorId) .
                ShippingRate::SAVED_FILE_NAME;
            try {
                $this->fileFactory->create(
                    ShippingRate::SAVED_FILE_NAME,
                    [
                        'type' => 'filename',
                        'value' => $projectPath
                    ]
                );
                $resultRaw = $this->resultRawFactory->create();

                return $resultRaw;
            } catch (\Exception $e) {
                $this->logger->error($e);
                $this->messageManager->addErrorMessage(__('Shipping rates file could not be downloaded'));

                return $redirect;
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select a vendor'));

            return $redirect;
        }
    }
}
