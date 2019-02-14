<?php
namespace Iredeem\Vendor\Controller\Adminhtml\VendorExport;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Iredeem\Vendor\Model\Export\VendorToCsv;
use Magento\Framework\App\Response\Http\FileFactory;

/**
 * Class GridToCsv
 * @package Iredeem\Vendor\Controller\Adminhtml\VendorExport
 */
class GridToCsv extends Action
{
    /**
     * @var VendorToCsv
     */
    protected $converter;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @param Context $context
     * @param VendorToCsv $converter
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        VendorToCsv $converter,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Export vendor to CSV
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        return $this->fileFactory->create('vendors.csv', $this->converter->getCsvFile(), 'var');
    }
}
