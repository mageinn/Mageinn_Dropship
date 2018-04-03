<?php
namespace Mageinn\Vendor\Model\Info;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use \Mageinn\Vendor\Model\ResourceModel\Info\CollectionFactory as InfoCollectionFactory;
use \Mageinn\Vendor\Model\ResourceModel\Address\CollectionFactory as AddressCollectionFactory;
use \Mageinn\Vendor\Model\Info;
use \Mageinn\Vendor\Model\Address;

/**
 * Class DataProvider
 *
 * @package Mageinn\Vendor\Model\Vendor
 */
class DataProvider extends AbstractDataProvider
{
    /** @var \Mageinn\Vendor\Model\ResourceModel\Address\Collection */
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
     * @codingStandardsIgnoreStart
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
    // @codingStandardsIgnoreEnd

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        /** @var \Mageinn\Vendor\Model\Info $vendor */
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

            $this->loadedData[$vendorId]['mageinn_vendor'] = $info;
            $this->loadedData[$vendorId]['vendor_settings'] = $settings;
            $this->loadedData[$vendorId]['vendor_address'] = $vendorAddresses;
            $this->loadedData[$vendorId]['batch_export'] = $batchExport;
            $this->loadedData[$vendorId]['batch_import'] = $batchImport;
        }

        return $this->loadedData;
    }
}
