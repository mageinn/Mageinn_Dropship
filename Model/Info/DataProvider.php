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
namespace Mageinn\Dropship\Model\Info;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use \Mageinn\Dropship\Model\ResourceModel\Info\CollectionFactory as InfoCollectionFactory;
use \Mageinn\Dropship\Model\ResourceModel\Address\CollectionFactory as AddressCollectionFactory;
use \Mageinn\Dropship\Model\Info;
use \Mageinn\Dropship\Model\Address;

/**
 * Class DataProvider
 * @package Mageinn\Dropship\Model\Info
 */
class DataProvider extends AbstractDataProvider
{
    /** @var \Mageinn\Dropship\Model\ResourceModel\Address\Collection */
    protected $addressCollection;

    /** @var array Settings fieldset */
    protected $settings = [
        'currency' => '',
        'notify_order' => '',
        'notify_order_email' => '',
    ];

    /**
     * @var array
     */
    protected $batchExportColumns = [
        'batch_export_enabled' => '',
        'batch_export_shipment_status' => '',
        'batch_export_schedule' => '',
        'batch_export_destination' => '',
        'batch_export_headings' => '',
        'batch_export_values' => '',
        'batch_export_private_key' => '',
    ];

    /**
     * @var array
     */
    protected $batchImportColumns = [
        'batch_import_enabled' => '',
        'batch_import_schedule' => '',
        'batch_import_source' => '',
        'batch_import_file_data' => '',
        'batch_import_delimiter' => '',
    ];

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param InfoCollectionFactory $infoCollectionFactory
     * @param AddressCollectionFactory $addressCollectionFactory
     * @param array $meta
     * @param array $data
     *
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        InfoCollectionFactory $infoCollectionFactory,
        AddressCollectionFactory $addressCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $infoCollectionFactory->create();
        $this->addressCollection = $addressCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        /** @var \Mageinn\Dropship\Model\Info $vendor */
        foreach ($items as $vendor) {
            $vendorId = $vendor->getEntityId();
            $info = [];
            $vendorAddresses = [];
            $batchExport = [];
            $info[Info::VENDOR_DATA_INFORMATION] = $vendor->getData();
            $settings[Info::VENDOR_DATA_SETTINGS] = array_intersect_key($vendor->getData(), $this->settings);
            $addresses = $this->addressCollection->addFieldToFilter('vendor_id', $vendorId)->getItems();
            foreach ($addresses as $address) {
                $vendorAddresses[$address->getType()] = $address->getData();
            }
            $vendorAddresses[Address::ADDRESS_TYPE_SHIPPING]['same_as_billing'] = $vendor->getSameAsBilling();
            $batchExport[Info::VENDOR_BATCH_EXPORT_GENERAL] =
                array_intersect_key($vendor->getData(), $this->batchExportColumns);
            $batchImport[Info::VENDOR_BATCH_IMPORT_GENERAL] =
                array_intersect_key($vendor->getData(), $this->batchImportColumns);

            $this->loadedData[$vendorId]['mageinn_dropship'] = $info;
            $this->loadedData[$vendorId]['vendor_settings'] = $settings;
            $this->loadedData[$vendorId]['vendor_address'] = $vendorAddresses;
            $this->loadedData[$vendorId]['batch_export'] = $batchExport;
            $this->loadedData[$vendorId]['batch_import'] = $batchImport;
        }

        return $this->loadedData;
    }
}
