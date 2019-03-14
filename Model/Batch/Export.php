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
namespace Mageinn\Dropship\Model\Batch;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class Export
 * @package Mageinn\Dropship\Model\Batch
 */
class Export extends AbstractModel
{
    /**
     * @var \Mageinn\Dropship\Helper\Data
     */
    protected $vendorHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $shipmentCollection;

    /**
     * @var \Mageinn\Dropship\Model\Batch\Export\Response
     */
    protected $response;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    protected $shipments;

    /**
     * @var \Mageinn\Dropship\Model\Batch\Export\File
     */
    protected $file;

    /**
     * @var array
     */
    protected $batchRowsArray;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * Export constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mageinn\Dropship\Helper\Data $helper
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollection
     * @param Export\Response $response
     * @param Export\File $file
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null $shipments
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Mageinn\Dropship\Helper\Data $helper,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollection,
        \Mageinn\Dropship\Model\Batch\Export\Response $response,
        \Mageinn\Dropship\Model\Batch\Export\File $file,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
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
        $this->serializer = $serializer;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param $vendor
     * @param $batchId
     * @return Export\Response|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process($vendor, $batchId)
    {
        $exportFields = $vendor->getBatchExportValues();
        if (preg_match_all('#\[([a-z0-9._]+)\]#i', $exportFields, $matches, PREG_PATTERN_ORDER)) {
            $placeholders = array_unique($matches[0]);
            $values = array_unique($matches[1]);
            $formattedData = $this->_getDataSource($vendor);
            $this->response->setRowsNumber(count($formattedData));
            if (!empty($formattedData)) {
                $fileHeading = $this->_getFileHeading($vendor);
                $fileContents = $this->_getFileContents(
                    $formattedData,
                    $placeholders,
                    $values,
                    $exportFields,
                    $batchId
                );
                $exportFileContents = $fileHeading . $fileContents;

                $fileResponse = $this->file->handle($exportFileContents, $vendor);
                $this->_updateShipments($vendor);
                $this->response->setRowsText($exportFileContents)
                    ->setNotes($fileResponse->getNotes())
                    ->setFilePath($fileResponse->getFilePath())
                    ->setBatchRows($this->batchRowsArray);
            }

            return $this->response;
        }

        return null;
    }

    /**
     * @param $vendor
     * @return string
     */
    protected function _getFileHeading($vendor)
    {
        $heading = $vendor->getBatchExportHeadings();
        if ($heading) {
            $heading .= PHP_EOL;
        }

        return $heading;
    }

    /**
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
            $replace = $this->_getDataForReplace($values, $dataItem);
            $fileContents .=
                $this->_getDataRow($placeholders, $replace, $exportFields, ($dataItem === end($formattedData)));
            $this->batchRowsArray[] = $this->_getBatchRowArray($dataItem, $batchId);
        }

        return $fileContents;
    }

    /**
     * @param $values
     * @param $dataItem
     * @return array
     */
    protected function _getDataForReplace($values, $dataItem)
    {
        $replace = [];
        foreach ($values as $value) {
            $split = explode('.', $value);
            if (count($split) === 2 && isset($dataItem[$split[0]][$split[1]])) {
                $replace[] = $dataItem[$split[0]][$split[1]];
            } elseif (count($split) === 1 && isset($dataItem[$split[0]])) {
                $replace[] = $dataItem[$split[0]];
            } else {
                $replace[] = '';
            }
        }

        return $replace;
    }

    /**
     * @param $placeholders
     * @param $replace
     * @param $exportFields
     * @param $isLast
     * @return mixed|string
     */
    protected function _getDataRow($placeholders, $replace, $exportFields, $isLast)
    {
        $stringTemp = str_replace($placeholders, $replace, $exportFields);
        if (!$isLast) {
            $stringTemp .= PHP_EOL;
        }

        return $stringTemp;
    }

    /**
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
     * @param $vendor
     */
    protected function _getShipments($vendor)
    {
        $statuses = explode(',', $this->vendorHelper->getBatchOrderExportConfig('shipment_statuses'));

        $shipments = $this->shipmentCollection->create()
            ->addFieldToFilter('vendor_id', ['eq' => $vendor->getId()])
            ->addFieldToFilter('dropship_status', ['in' => $statuses]);

        $this->shipments = $shipments;
    }

    /**
     * @return array
     */
    protected function _processShipments()
    {
        $data = [];
        if ($this->shipments) {
            foreach ($this->shipments as $shipment) {
                $items = $shipment->getItems();
                foreach ($items as $item) {
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
     * @param $vendor
     */
    protected function _updateShipments($vendor)
    {
        foreach ($this->shipments as $shipment) {

            $shipment->setDropshipStatus($vendor->getBatchExportShipmentStatus())
                ->setPackages($this->serializer->unserialize($shipment->getPackages()))
                ->save();

        }
    }

    /**
     * @param $dataItem
     * @param $batchId
     * @return array
     */
    protected function _getBatchRowArray($dataItem, $batchId)
    {
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
