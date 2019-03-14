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
namespace Mageinn\Dropship\Controller\Adminhtml\VendorExport;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mageinn\Dropship\Model\Export\VendorToCsv;
use Magento\Framework\App\Response\Http\FileFactory;

/**
 * Class GridToCsv
 * @package Mageinn\Dropship\Controller\Adminhtml\VendorExport
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
