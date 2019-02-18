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
namespace Mageinn\Dropship\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\Io\File;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

/**
 * Class Rates
 * @package Mageinn\Dropship\Helper
 */
class Rates extends CoreData
{

    /**
     * @var array
     */
    protected $errorMessages;

    /**
     * @var File
     */
    protected $fileSystem;

    /**
     * @var array
     */
    protected $allowedFileExtensions = ['csv'];

    /**
     * Rates constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param File $file
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        File $file
    ) {
        $this->fileSystem = $file;
        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * @param $errorMessages
     * @return $this
     */
    public function setErrorMessages($errorMessages)
    {
        $this->errorMessages = $errorMessages;
        return $this;
    }

    /**
     * @param $message
     */
    public function addErrorMessage($message)
    {
        $this->errorMessages[] = $message;
    }

    /**
     * @return void
     */
    public function clearErrorMessages()
    {
        $this->errorMessages = [];
    }

    /**
     * @param $value
     * @param $row
     * @return bool
     */
    public function checkIsNumber($value, $row)
    {
        if (!is_numeric($value)) {
            $this->addErrorMessage(__('Value %1 declared on row #%2 is not a number', $value, $row));

            return false;
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function isCsv($file)
    {
        $fileDetails = $this->fileSystem->getPathInfo($file);

        if (!in_array($fileDetails['extension'], $this->allowedFileExtensions)) {
            return false;
        }

        return true;
    }

    /**
     * @param $sku
     * @param $vendorSku
     * @param $row
     * @return bool
     */
    public function checkSkuBelongToVendor($sku, $vendorSku, $row)
    {
        if (!in_array($sku, $vendorSku)) {
            $this->addErrorMessage(__('Sku %1 declared on row #%2 does not exist', "'" . $sku . "'", $row));

            return false;
        }
        return true;
    }
}