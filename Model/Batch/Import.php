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
use \Magento\Framework\Exception\LocalizedException;

/**
 * Class Import
 * @package Mageinn\Dropship\Model\Batch
 */
class Import extends AbstractModel
{
    const CONFIGURATION_BATCH_IMPORT_ORDER_STATUS = 'dropship/batch_order_import/order_status';

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @var \Magento\Sales\Model\Order\Shipment
     */
    protected $shipment;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackFactory
     */
    protected $track;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $defaultImportStatus;

    /**
     * @var array
     */
    protected $idTypes = ['po_id', 'order_id'];

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * Import constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->csvProcessor = $csvProcessor;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->track = $trackFactory;
        $this->orderFactory = $orderFactory;
        $this->scopeConfig = $scopeConfig;
        $this->fileDriver = $fileDriver;
        $this->serializer = $serializer;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param $source
     * @param $template
     * @param $delimiter
     * @return array|bool
     * @throws LocalizedException
     */
    public function getImportData($source, $template, $delimiter)
    {
        if (is_file($source)) {
            $this->csvProcessor->setDelimiter($delimiter);

            try {
                $fileData = $this->csvProcessor->getData($source);
            } catch (\Exception $e) {
                throw new LocalizedException(__($e->getMessage()));
            }

            if (count($fileData[0]) !== count($template)) {
                throw new LocalizedException(__('Invalid file row format'));
            }
            if (array_intersect($template, $fileData[0])) {
                array_shift($fileData);
            }

            $clear = [];
            foreach ($fileData as $data) {
                if ($data) {
                    $clear[] = array_combine($template, $data);
                }
            }

            return $clear;
        }

        return false;
    }

    /**
     * @param $template
     * @param $delimiter
     * @return mixed
     * @throws LocalizedException
     */
    public function validateImportTemplate($template, $delimiter)
    {
        if (!preg_match_all('#\[([^]]+)\]([^[]+)?#', $template, $matches, PREG_PATTERN_ORDER)) {
            throw new LocalizedException(__('Invalid import template'));
        }
        if (!in_array('po_id', $matches[1])
            && !in_array('order_id', $matches[1])
            || !in_array('tracking_id', $matches[1])
        ) {
            throw new LocalizedException(__('Missing required field'));
        }
        if (in_array('po_id', $matches[1]) && in_array('order_id', $matches[1])) {
            throw new LocalizedException(__('Either po_id OR order_id can be specified, but not both'));
        }
        array_pop($matches[2]);
        array_walk($matches[2], function ($value, $key, $delimiter) {
            if ($value !== $delimiter) {
                throw new LocalizedException(__('Template contains invalid delimiter'));
            }
        }, $delimiter);

        return $matches[1];
    }

    /**
     * @param $shipmentData
     * @param $template
     * @param $batchId
     * @param $vendorId
     * @param $currentDate
     * @return $this
     */
    public function updateShipmentBatch($shipmentData, $template, $batchId, $vendorId, $currentDate)
    {
        $idType = current(array_intersect($this->idTypes, $template));

        $this->defaultImportStatus = $this->scopeConfig->getValue(
            self::CONFIGURATION_BATCH_IMPORT_ORDER_STATUS
        );

        $batchRows = [];
        foreach ($shipmentData as $data) {
            $trackData = null;
            $error = null;
            $shipment = $this->_getShipment($data[$idType], $idType, $vendorId);

            if ($shipment && $shipment->hasData()) {
                $shippingDate = isset($data['shipping_date']) ? $data['shipping_date'] : $currentDate;
                if ($trackData = $this->_getTrack($data)) {
                    $shipment->addTrack($this->track->create()->addData($trackData));
                }

                $shipment
                    ->setDropshipStatus($this->defaultImportStatus)
                    ->setShippingDate($shippingDate)
                    ->setPackages($this->serializer->unserialize($shipment->getPackages()))
                    ->save();
                $batchRows[] = $this->_getBatchRowArray($batchId, $trackData, $shipment, $error);
            } else {
                $this->setBatchRowError(true);
                $error = 'Invalid order or shipment id provided: ' . implode(' ', $data);
                $batchRows[] = $this->_getBatchRowArray($batchId, $data, null, $error);
                $this->_logger->warning('Error for vendor with ID: ' . $vendorId . ' -> ' . $error);
            }
        }

        $this->setBatchRows($batchRows);

        return $this;
    }

    /**
     * @param $id
     * @param $type
     * @param null $vendorId
     * @return \Magento\Framework\DataObject|null
     */
    protected function _getShipment($id, $type, $vendorId = null)
    {
        switch ($type) {
            case 'po_id':
                $shipment = $this->shipmentCollectionFactory->create()
                    ->addFieldToFilter('increment_id', ['eq' => $id])->getFirstItem();
                break;
            case 'order_id':
                $order = $this->orderFactory->create()->loadByIncrementId($id);
                $shipment = $this->shipmentCollectionFactory->create()
                    ->setOrderFilter($order)->addFieldToFilter('vendor_id', ['eq' => $vendorId])->getFirstItem();
                break;
            default:
                $shipment = null;
        }

        return $shipment;
    }

    /**
     * @param $data
     * @return array|null
     */
    protected function _getTrack($data)
    {
        $trackData = null;
        if (isset($data['tracking_id']) && !empty($data['tracking_id'])) {
            $trackData = [
                'number' => $data['tracking_id'],
                'carrier_code' => isset($data['carrier']) ? $data['carrier'] : '',
                'title' => isset($data['title']) ? $data['title'] : '',
            ];
        }

        return $trackData;
    }

    /**
     * @param $batchId
     * @param $trackData
     * @param null $shipment
     * @param null $error
     * @return array
     */
    protected function _getBatchRowArray($batchId, $trackData, $shipment = null, $error = null)
    {
        if ($shipment && $shipment->hasData()) {
            $order = $shipment->getOrder();
            $batchRow = [
                'batch_id' => $batchId,
                'order_id' => $shipment->getOrderId(),
                'shipment_id' => $shipment->getId(),
                'track_id' => $trackData['number'],
                'order_increment_id' => $order->getIncrementId(),
                'shipment_increment_id' => $shipment->getIncrementId(),
                'has_error' => 0,
                'error_info' => '',
            ];
        } else {
            $batchRow = [
                'batch_id' => $batchId,
                'order_id' => null,
                'shipment_id' => null,
                'order_increment_id' => '',
                'shipment_increment_id' => isset($trackData['po_id']) ? $trackData['po_id'] : '',
                'track_id' => $trackData['tracking_id'],
                'has_error' => $error ? 1 : 0,
                'error_info' => $error,
            ];
        }

        return $batchRow;
    }
}
