<?php
namespace Mageinn\Dropship\Controller\Adminhtml\Stock;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Mageinn\Dropship\Helper\Rates;
use Psr\Log\LoggerInterface;
use Mageinn\Dropship\Helper\Stock;

/**
 * Class Import
 * @package Mageinn\Dropship\Controller\Adminhtml\Stock
 * @codeCoverageIgnore Controller functions don't need UT
 */
class Import extends Action
{
    /**
     * @var Csv
     */
    protected $csvProcessor;

    /**
     * @var Rates
     */
    protected $shippingRatesHelper;

    /**
     * @var WriteInterface
     */
    protected $filesystem;

    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var \Mageinn\Dropship\Helper\Stock
     */
    protected $stockHelper;


    /**
     * Import constructor.
     * @param Context $context
     * @param Csv $processor
     * @param Rates $shippingRates
     * @param LoggerInterface $logger
     * @param Stock $stockHelper
     */
    public function __construct(
        Context $context,
        Csv $processor,
        Rates $shippingRates,
        LoggerInterface $logger,
        Stock $stockHelper
    ) {
        $this->csvProcessor = $processor;
        $this->shippingRatesHelper = $shippingRates;
        $this->logger = $logger;
        $this->stockHelper = $stockHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $vendorId = $this->getRequest()->getParam('vendor_id');
        $file = $this->getRequest()->getFiles('produc_stock_file');

        if ($vendorId && !$file['error'] && $file['size'] && $this->shippingRatesHelper->isCsv($file['name'])) {
            try {
                $stockArray = $this->csvProcessor->getData($file['tmp_name']);
                if ($this->stockHelper->process($vendorId, $stockArray)) {
                    $this->messageManager->addSuccessMessage(__('Stock have been successfully updated'));
                }
            } catch (LocalizedException $e) {
                // Could be caught while the stock is saved in the DB
                $this->logger->error($e);
                $this->messageManager->addErrorMessage(
                    __('An issue occurred while adding stock into the  system')
                );
            } catch (\Exception $e) {
                // Could be caught for getting the csv data from the file or saving the file
                $this->logger->error($e);
                $this->messageManager->addErrorMessage(__('Selected vendor has no products associated'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select a vendor and an import file in csv format'));
        }

        $this->_addStockErrorMessages($this->shippingRatesHelper->getErrorMessages());
        $redirect = $this->resultRedirectFactory->create();

        return $redirect->setPath('*/*/');
    }

    /**
     * @param $messagesArray
     */
    protected function _addStockErrorMessages($messagesArray)
    {
        if (!empty($messagesArray)) {
            foreach ($messagesArray as $message) {
                $this->messageManager->addErrorMessage($message);
            }
        }
    }
}
