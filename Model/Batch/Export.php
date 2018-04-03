<?php

namespace Mageinn\Vendor\Model\Batch;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class Adapter
 *
 * @package Mageinn\Vendor\Model\Batch
 */
class Export extends AbstractModel
{
    /**
     * @var \Mageinn\Vendor\Helper\Data
     */
    protected $vendorHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $shipmentCollection;

    /**
     * @var \Mageinn\Vendor\Model\Batch\Export\Response
     */
    protected $response;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    protected $shipments;

    /**
     * @var \Mageinn\Vendor\Model\Batch\Export\File
     */
    protected $file;

    /**
     * @var array
     */
    protected $batchRowsArray;

    /**
     * Export constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mageinn\Vendor\Helper\Data $helper
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollection
     * @param \Mageinn\Vendor\Model\Batch\Export\Response $response
     * @param \Mageinn\Vendor\Model\Batch\Export\File $file
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $shipments
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Mageinn\Vendor\Helper\Data $helper,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollection,
        \Mageinn\Vendor\Model\Batch\Export\Response $response,
        \Mageinn\Vendor\Model\Batch\Export\File $file,
        \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $shipments = null,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        $data = []
    ) {
        $this->vendorHelper = $helper;
        $this->shipmentCollection= $shipmentCollection;
        $this->response = $response;
        $this->file = $file;
        $this->shipments = $shipments;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Processes the shipments based on the vendor
     *
     * @param $vendor
     * @param $batchId
     * @return \Mageinn\Vendor\Model\Batch\Export\Response|null
     * @throws \Exception
     */
    public function process($vendor, $batchId)
    {
        $exportFields = $vendor->getBatchExportValues();
        if (preg_match_all('#\[([a-z0-9._]+)\]#i', $exportFields, $matches, PREG_PATTERN_ORDER)) {
            // First array of matches, an array of the full strings found in the match
            // Used in the future as the placeholders which will be replaced
            $placeholders = array_unique($matches[0]);
            // Second array of matches, the matches contained between () in the regex
            // Used in the future for formatting the data which will be added
            // instead of the placeholders using the values in the array
            $values = array_unique($matches[1]);
            // Formatted data for each item of the vendor's shipments
            $formattedData = $this->_getDataSource($vendor);
            $fileHeading = $this->_getFileHeading($vendor);
            $fileContents = '';
            if (!empty($formattedData)) {
                $fileContents =
                    $this->_getFileContents($formattedData, $placeholders, $values, $exportFields, $batchId);
            }
            $exportFileContents = $fileHeading . $fileContents;

            // Create file and send it in emails based on the vendor settings
            $fileResponse = $this->file->handle($exportFileContents, $vendor);
            $this->_updateShipments($vendor);
            $this->response->setRowsNumber(count($formattedData))
                ->setRowsText($exportFileContents)
                ->setNotes($fileResponse->getNotes())
                ->setFilePath($fileResponse->getFilePath())
                ->setBatchRows($this->batchRowsArray);

            return $this->response;
        }

        return null;
    }

    /**
     * Returns the heading of the file
     *
     * @param $vendor
     * @return string
     */
    protected function _getFileHeading($vendor)
    {
        $heading = $vendor->getBatchExportHeadings();
        // If the vendor has the heading set for it, add the EOL constant at the end
        if ($heading) {
            $heading .= PHP_EOL;
        }

        return $heading;
    }

    /**
     * Returns the contents which will be added to the file
     *
     * @param $formattedData
     * @param $placeholders
     * @param $values
     * @param $exportFields
     * @param $batchId
     * @return string
     */
    protected function _getFileContents($formattedData, $placeholders, $values, $exportFields, $batchId)
    {
        $fileContents = '';
        $this->batchRowsArray = [];
        foreach ($formattedData as $dataItem) {
            // Using the values array for each shipment item to get the data required
            $replace = $this->_getDataForReplace($values, $dataItem);
            // Based on the placeholders array, the replace data and the vendor setting for the values in the export
            // file, each row of the file is formed
            $fileContents .=
                $this->_getDataRow($placeholders, $replace, $exportFields, ($dataItem === end($formattedData)));
            // Add a batch row array for each data item, so that they will be added in the database later
            $this->batchRowsArray[] = $this->_getBatchRowArray($dataItem, $batchId);
        }

        return $fileContents;
    }

    /**
     * Returns the data which will replace the placeholders in the export fields
     *
     * @param $values
     * @param $dataItem
     * @return array
     */
    protected function _getDataForReplace($values, $dataItem)
    {
        $replace = [];
        foreach ($values as $value) {
            $split = explode('.', $value);
            // If the value is in the form of entity.value, the corresponding value (array[entity][value])
            // from the item data is taken
            // Otherwise, if a shorthand notation is used, the array[notation] value is taken
            // Otherwise empty string provided for the values not found in the data array so as to be able
            // to replace all values found for the matches
            // @codingStandardsIgnoreStart
            if (count($split) === 2 && isset($dataItem[$split[0]][$split[1]])) {
                $replace[] = $dataItem[$split[0]][$split[1]];
            } elseif (count($split) === 1 && isset($dataItem[$split[0]])) {
                $replace[] = $dataItem[$split[0]];
            } else {
                $replace[] = '';
            }
            // @codingStandardsIgnoreEnd
        }

        return $replace;
    }

    /**
     * Returns the data for a row of the export file
     *
     * @param $placeholders
     * @param $replace
     * @param $exportFields
     * @param $isLast
     * @return mixed|string
     */
    protected function _getDataRow($placeholders, $replace, $exportFields, $isLast)
    {
        $stringTemp = str_replace($placeholders, $replace, $exportFields);
        // If the row is not the last one, the EOL is added
        if (!$isLast) {
            $stringTemp .= PHP_EOL;
        }

        return $stringTemp;
    }

    /**
     * Returns the formatted data for the shipments
     *
     * @param $vendor
     * @return array
     */
    protected function _getDataSource($vendor)
    {
        $this->_getShipments($vendor);
        $shipmentItemsData = $this->_processShipments();

        return $shipmentItemsData;
    }

    /**
     * Returns the shipments for the vendor which have the statuses specified in the config
     *
     * @param $vendor
     */
    protected function _getShipments($vendor)
    {
        $statuses = explode(',', $this->vendorHelper->getBatchOrderExportConfig('shipment_statuses'));

        $shipments = $this->shipmentCollection->create()
            ->addFieldToFilter('vendor_id', ['eq' => $vendor->getId()])
            ->addFieldToFilter('dropship_status', ['in' => $statuses]);

        // Setting the shipments class property so that we can update them in one go
        $this->shipments = $shipments;
    }

    /**
     * Returns data for each shipping item
     *
     * @return array
     */
    protected function _processShipments()
    {
        $data = [];
        if ($this->shipments) {
            foreach ($this->shipments as $shipment) {
                $items = $shipment->getItems();
                // Go through each shipment item and add the necessary data for it
                foreach ($items as $item) {
                    // The data contains item specific info as well as shipment specific info, which means that
                    // depending on weather a shipment has multiple items, the returned data may contain identical
                    // data values when it comes to shipment specific info
                    $itemData = [
                        'item' => $item->getData(),
                        'order' => $shipment->getOrder()->getData(),
                        'shipping' => $shipment->getShippingAddress()->getData(),
                        'billing' => $shipment->getBillingAddress()->getData(),
                        'order_id' => $shipment->getOrder()->getIncrementId(),
                        'po_id' => $shipment->getIncrementId(),
                    ];

                    $data[] = $itemData;
                }
            }
        }

        return $data;
    }

    /**
     * Updates the current shipments by changing their status
     *
     * @param $vendor
     */
    protected function _updateShipments($vendor)
    {
        $this->shipments->getConnection()->update(
            $this->shipments->getMainTable(),
            ['dropship_status' => $vendor->getBatchExportShipmentStatus()],
            sprintf(
                'vendor_id = %s and dropship_status in (%s)',
                $vendor->getId(),
                $this->vendorHelper->getBatchOrderExportConfig('shipment_statuses')
            )
        );
    }

    /**
     * Creates a batch_row entity array which will be inserted in the DB later
     *
     * @param $dataItem
     * @param $batchId
     * @return array
     */
    protected function _getBatchRowArray($dataItem, $batchId)
    {
        // Some properties are set to null. If in the future they will be requested for
        // this functionality (batch export), they will be added
        $batchRow = [
            'batch_id' => $batchId,
            'order_id' => isset($dataItem['order']['increment_id']) ? $dataItem['order']['increment_id'] : '',
            'shipment_id' => isset($dataItem['po_id']) ? $dataItem['po_id'] : '',
            'item_id' => isset($dataItem['item']['entity_id']) ? $dataItem['item']['entity_id'] : '',
            'track_id' => '',
            'order_increment_id' => isset($dataItem['order']['increment_id']) ? $dataItem['order']['increment_id']: '',
            'shipment_increment_id' => isset($dataItem['po_id']) ? $dataItem['po_id'] : '',
            'item_sku' => isset($dataItem['item']['sku']) ? $dataItem['item']['sku'] : '',
            'tracking_id' => '',
            'has_error' => 0,
            'error_info' => '',
            'row_json' => '',
        ];

        return $batchRow;
    }
}
