<?php

namespace Mageinn\Dropship\Model\Export;

use Mageinn\Dropship\Model\Address;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class VendorToCsv
 * @package Mageinn\Dropship\Model\Export
 */
class VendorToCsv
{
    /**
     * @var WriteInterface
     */
    protected $directory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var array
     */
    protected $vendor = [
        'name', 'email', 'telephone', 'notify_order', 'currency', 'client_managed', 'multiplier', 'same_as_billing',
        'batch_export_enabled', 'batch_export_schedule', 'batch_import_enabled', 'batch_import_schedule'
    ];

    /**
     * @var array
     */
    protected $billing = [
        'contact_name', 'street', 'city', 'postal_code', 'country', 'region', 'region_id'
    ];

    /**
     * @var array
     */
    protected $shipping = [
        'contact_name', 'street', 'city', 'postal_code', 'country', 'region', 'region_id'
    ];

    /**
     * @var array
     */
    protected $customerService = [
        'email', 'telephone', 'street', 'city', 'postal_code', 'country', 'url_title', 'url'
    ];

    /**
     * VendorToCsv constructor.
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter
    ) {
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * Get CSV file
     *
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCsvFile()
    {
        $component = $this->filter->getComponent();

        // @codingStandardsIgnoreStart
        $name = md5(microtime());
        // @codingStandardsIgnoreEnd
        $file = 'export/vendor' . $name . '.csv';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();
        $collection = $dataProvider->getSearchResult();
        $collection->getSelect()->joinLeft(
            ['billing_address' => $collection->getTable(Address::VENDOR_ADDRESS_TABLE)],
            'main_table.entity_id = billing_address.vendor_id and billing_address.type = "billing_address"',
            array_map(function ($value) {
                return $value . ' AS billing_' . $value;
            }, $this->billing)
        )->joinLeft(
            ['shipping_address' => $collection->getTable(Address::VENDOR_ADDRESS_TABLE)],
            'main_table.entity_id = shipping_address.vendor_id and shipping_address.type = "shipping_address"',
            array_map(function ($value) {
                return $value . ' AS shipping_' . $value;
            }, $this->shipping)
        )->joinLeft(
            ['customer_service' => $collection->getTable(Address::VENDOR_ADDRESS_TABLE)],
            'main_table.entity_id = customer_service.vendor_id and customer_service.type = "customer_service"',
            array_map(function ($value) {
                return $value . ' AS customer_service_' . $value;
            }, $this->customerService)
        );

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        // @codingStandardsIgnoreStart
        $stream->writeCsv(array_keys($this->_cleanExportData($collection->getFirstItem()->getData())));
        // @codingStandardsIgnoreEnd
        foreach ($collection as $item) {
            $stream->writeCsv($this->_cleanExportData($item->getData()));
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

    /**
     * Clean Vendor Data
     *
     * @param $exportData
     * @return array
     */
    protected function _cleanExportData($exportData)
    {
        $billing = array_map(function ($value) {
            return 'billing_' . $value;
        }, $this->billing);
        $shipping = array_map(function ($value) {
            return 'shipping_' . $value;
        }, $this->shipping);
        $customerService = array_map(function ($value) {
            return 'customer_service_' . $value;
        }, $this->customerService);
        $exportFields = array_merge($this->vendor, $billing, $shipping, $customerService);

        $data = [];
        foreach ($exportFields as $exportField) {
            $data[$exportField] = $exportData[$exportField];
        }

        return $data;
    }
}
