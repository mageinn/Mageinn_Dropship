<?php
namespace Mageinn\Vendor\Model\Batch;

/**
 * Class DataProvider
 * @package Mageinn\Vendor\Model\Batch
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * @var \Mageinn\Vendor\Helper\Data
     */
    protected $_vendorHelper;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Mageinn\Vendor\Model\ResourceModel\Batch\CollectionFactory $collectionFactory
     * @param \Mageinn\Vendor\Helper\Data $vendorHelper
     * @param array $meta
     * @param array $data
     *
     * @codingStandardsIgnoreStart
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Mageinn\Vendor\Model\ResourceModel\Batch\CollectionFactory $collectionFactory,
        \Mageinn\Vendor\Helper\Data $vendorHelper,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->_vendorHelper = $vendorHelper;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    // @codingStandardsIgnoreEnd

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }

        $info = [];
        $batchItems = $this->collection->getItems();

        /** @var \Mageinn\Vendor\Model\Batch $item */
        foreach ($batchItems as $item) {
            // Set vendor name for the data
            $item->setVendor($this->_vendorHelper->getVendorNameById($item->getVendorId()));
            $info[\Mageinn\Vendor\Model\Batch::BATCH_DATA_INFORMATION] = $item->getData();
            $this->_loadedData[$item->getId()] = $info;
        }

        return $this->_loadedData;
    }
}
