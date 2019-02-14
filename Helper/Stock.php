<?php
namespace Iredeem\Vendor\Helper;

use Iredeem\Vendor\Helper\CoreData;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class ShippingRates
 * @package Iredeem\DeliveryCountry\Helper
 * @codeCoverageIgnore
 */
class Stock extends CoreData
{
    const HEADINGS_ROW_NUMBER = 0;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Rates
     */
    protected $ratesHelper;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var Magento\Framework\File\Csv
     */
    protected $csvProcessor;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * @var  /Iredeem\Vendor\Helper\Data
     */
    protected $vendorHelper;

    /**
     * @var Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistryInterface;

    const FILE_SAVE_PATH = 'productStock/%s/';


    /**
     * Stock constructor.
     * @param Rates $ratesHelper
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param ProductCollectionFactory $productCollectionFactory
     * @param StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Data $vendorHelper
     * @param StockRegistryInterface $stockRegistryInterface
     */
    public function __construct(
        \Iredeem\Vendor\Helper\Rates $ratesHelper,
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        ProductCollectionFactory $productCollectionFactory,
        StockRegistryInterface $stockRegistry,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem,
        \Iredeem\Vendor\Helper\Data $vendorHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
    ) {
        $this->ratesHelper = $ratesHelper;
        $this->stockRegistry = $stockRegistry;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->csvProcessor = $csvProcessor;
        $this->vendorHelper = $vendorHelper;
        $this->stockRegistryInterface = $stockRegistryInterface;

        parent::__construct($context, $objectManager, $storeManager);
    }


    /**
     * @param $vendorId
     * @param $csvContent
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function process($vendorId, $csvContent)
    {
        $result = true;
        $this->ratesHelper->clearErrorMessages();

        foreach ($csvContent as $row => $productStock) {
            // Skip checking the headings row
            if ($row == self::HEADINGS_ROW_NUMBER) {
                continue;
            }
            $row++;

            $availableSku = $this->getVendorProductSku($this->getVendorProductsCollection($vendorId));
            //check if a vendor has any product associated
            if (!$availableSku) {
                $result = false;
            }
            //if a sku not belong to a vendor skip that sku
            if (!$this->ratesHelper->checkSKuBelongToVendor($productStock[0], $availableSku, $row)) {
                continue;
            }

            //check if the quantity is a number
            if (!$this->ratesHelper->checkIsNumber($productStock[1], $row)) {
                continue;
            }

            $this->updateProductStock($productStock);
        }
        return $result;
    }

    /**
     * @param $productStock
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function updateProductStock($productStock)
    {
        $stockItem = $this->stockRegistry->getStockItemBySku($productStock[0]);
        $stockItem->setQty($productStock[1]);
        $stockItem->setIsInStock((bool)$productStock[1]); // this line
        $this->stockRegistry->updateStockItemBySku($productStock[0], $stockItem);
    }

    /** Get vendor products collection
     * @param $vendorId
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getVendorProductsCollection($vendorId)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->getSelect()->reset(\Zend_Db_Select::COLUMNS)->columns(['sku', 'entity_id']);
        $productCollection->addFieldToFilter('vendor_id', ['in' => $vendorId]);

        return $productCollection;
    }

    /**
     * @param $collection
     * @return array|bool
     */
    private function getVendorProductSku($collection)
    {
        $skuList = [];
        foreach ($collection as $product) {
            array_push($skuList, $product->getSku());
        }
        if (!empty($skuList)) {
            return $skuList;
        }

        return false;
    }

    /**
     * @param $vendorId
     * @param $projectPath
     * @param $fileName
     * @return bool
     */
    public function exportStock($vendorId, $projectPath, $fileName)
    {
        $stockArray = [];
        $stockItem = [];
        $productCount = 0;
        //set file header
        $stockArray[$productCount]['sku'] = 'SKU';
        $stockArray[$productCount]['stock_qty'] = 'Stock Quantity';
        foreach ($this->getVendorProductsCollection($vendorId) as $product) {
            $productCount++;

            //check if the product has manage stock, if yes continue
            if (!$this->getManageStockStatus($product->getId())) {
                continue;
            }
            $stockItem['sku'] = $product->getSku();
            $stockItem['stock_qty'] = $this->getProductStock($product->getId(), $product);
            $stockArray [] = $stockItem;
        }

        if ($productCount == 0) {
            return false;
        }
        // create csv file with the export
        $this->writeToCsv($stockArray, $projectPath, $fileName);

        return true;
    }


    /**
     * @param $productId
     * @return  int
     */
    public function getProductStock($productId)
    {
        $stock_item = $this->stockRegistryInterface->getStockItem($productId);

        return $stock_item->getQty();
    }

    /**Create csv directory
     * @param $data
     * @param $projectPath
     * @param $fileName
     * @return bool
     *
     */

    public function writeToCsv($data, $projectPath, $fileName)
    {
        //@codingStandardsIgnoreStart
        if (!is_dir($projectPath)) {
            mkdir($projectPath, 0777, true);
        }
        //@codingStandardsIgnoreStop
        // create csv file with vendor name
        $filePath = $projectPath . '/' . $fileName;
        $this->csvProcessor
            ->setEnclosure('"')
            ->setDelimiter(',')
            ->saveData($filePath, $data);

        return true;
    }

    /** Check product manage stock status
     * @param $productId
     * @return bool
     */
    public function getManageStockStatus($productId)
    {
        $stock_item = $this->stockRegistryInterface->getStockItem($productId);

        if ($stock_item->getManageStock() == 1) {
            return true;
        } else {
            return false;
        }
    }
}