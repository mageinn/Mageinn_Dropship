<?php
namespace Mageinn\Vendor\Controller\Adminhtml\ShippingRates;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\File\Csv;
use \Mageinn\Vendor\Helper\ShippingRates;
use \Mageinn\Vendor\Model\ResourceModel\ShippingRate as ShippingRateResourceModel;
use \Mageinn\Vendor\Model\ShippingRate;
use \Magento\Framework\File\UploaderFactory;
use \Magento\Framework\Filesystem\Directory\WriteInterface;
use \Magento\Framework\Filesystem;
use \Magento\Framework\App\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;

/**
 * Class Import
 * @package Mageinn\Vendor\Controller\Adminhtml\ShippingRates
 */
class Import extends Action
{
    /**
     * @var Csv
     */
    protected $csvProcessor;

    /**
     * @var ShippingRates
     */
    protected $shippingRatesHelper;

    /**
     * @var ShippingRateResourceModel
     */
    protected $shippingRateResource;

    /**
     * @var UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var WriteInterface
     */
    protected $filesystem;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Import constructor.
     * @param Context $context
     * @param Csv $processor
     * @param ShippingRates $shippingRates
     * @param ShippingRateResourceModel $shippingRate
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        Csv $processor,
        ShippingRates $shippingRates,
        ShippingRateResourceModel $shippingRate,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->csvProcessor = $processor;
        $this->shippingRatesHelper = $shippingRates;
        $this->shippingRateResource = $shippingRate;
        $this->fileUploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Import shipping rates action
     */
    public function execute()
    {
        $vendorId = $this->getRequest()->getParam('vendor_id');
        $file = $this->getRequest()->getFiles('shipping_rates_file');

        if ($vendorId && !$file['error'] && $file['size'] && $this->shippingRatesHelper->isCsv($file['name'])) {
            try {
                $importRatesArray = $this->csvProcessor->getData($file['tmp_name']);

                if ($this->shippingRatesHelper->process($vendorId, $importRatesArray)) {
                    $countries = $this->shippingRatesHelper->getInsertRows();
                    $this->shippingRatesHelper->bulkDeleteAndInsert(
                        $this->shippingRateResource,
                        sprintf('vendor_id = %s', $vendorId),
                        $countries
                    );
                    $this->_uploadFile($vendorId, $file);

                    $this->messageManager->addSuccessMessage(__('Shipping Rates have been imported successfully'));
                } else {
                    $this->_addErrorMessages($this->shippingRatesHelper->getErrorMessages());
                }
            } catch (LocalizedException $e) {
                // Could be caught while the rates are saved in the DB
                $this->logger->error($e);
                $this->messageManager->addErrorMessage(
                    __('An issue occurred while adding shipping rates to the system')
                );
            } catch (\Exception $e) {
                // Could be caught for getting the csv data from the file or saving the file
                $this->logger->error($e);
                $this->messageManager->addErrorMessage(__('An issue occurred while saving shipping rates'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select a vendor and an import file in csv format'));
        }

        $redirect = $this->resultRedirectFactory->create();

        return $redirect->setPath('*/*/');
    }

    /**
     * Adds error messages from the checks
     *
     * @param $messagesArray
     */
    protected function _addErrorMessages($messagesArray)
    {
        foreach ($messagesArray as $message) {
            $this->messageManager->addErrorMessage($message);
        }
    }

    /**
     * Uploads a file in the system
     *
     * @param $vendorId
     * @param $file
     * @throws \Exception
     */
    protected function _uploadFile($vendorId, $file)
    {
        $target = $this->filesystem->getAbsolutePath(sprintf(ShippingRate::FILE_SAVE_PATH, $vendorId));
        $uploader = $this->fileUploaderFactory->create(['fileId' => $file]);
        $uploader->setAllowedExtensions(['csv']);
        $uploader->setAllowRenameFiles(false);
        $uploader->save($target, ShippingRate::SAVED_FILE_NAME);
    }
}
