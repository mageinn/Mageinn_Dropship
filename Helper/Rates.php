<?php
/**
 * Created by PhpStorm.
 * User: d1sho
 * Date: 13.02.19
 * Time: 9:49
 */

namespace Mageinn\Dropship\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\Io\File;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Rates extends CoreData
{
    const REGION_ARRAY_POSITION = 0;
    const COUNTRY_ARRAY_POSITION = 1;
    const SHIPPING_GROUP_ARRAY_POSITION = 2;
    const DELIVERY_TIME_ARRAY_POSITION = 3;
    const PRICE_ARRAY_POSITION = 4;
    const HEADINGS_ROW_NUMBER = 0;

    /**
     * @var array
     */
    protected $insertRows;

    /**
     * @var array
     */
    protected $errorMessages;

    /**
     * @var array
     */
    protected $regionsArray;

    /**
     * @var array
     */
    protected $countriesArray;

    /**
     * @var File
     */
    protected $fileSystem;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

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
     * @param ProductCollectionFactory $productCollectionFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        File $file,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->fileSystem = $file;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->regionsArray = [];
        $this->countriesArray = [];
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
     * @param array $errorMessages
     * @return ShippingRates
     */
    public function setErrorMessages($errorMessages)
    {
        $this->errorMessages = $errorMessages;
        return $this;
    }

    /**
     * @param string $message
     */
    public function addErrorMessage($message)
    {
        $this->errorMessages[] = $message;
    }

    /**
     * Clear the error messages
     */
    public function clearErrorMessages()
    {
        $this->errorMessages = [];
    }

    /**
     * Number check for time and rate values in CSV file
     *
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
     * Check is the file has the csv extension
     *
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