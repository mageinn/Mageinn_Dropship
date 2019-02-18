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
namespace Mageinn\Dropship\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AutomaticShipment
 * @package Mageinn\Dropship\Observer
 */
class AutomaticShipment implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Model\Order\ShipmentDocumentFactory
     */
    protected $shipmentFactory;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    protected $convertOrder;

    /**
     * @var \Mageinn\Dropship\Model\Info
     */
    protected $vendor;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * AutomaticShipment constructor.
     * @param \Magento\Sales\Model\Order\ShipmentDocumentFactory $shipmentFactory
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Sales\Model\Convert\Order $convertOrder
     * @param \Mageinn\Dropship\Model\Info $vendor
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Sales\Model\Order\ShipmentDocumentFactory $shipmentFactory,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Mageinn\Dropship\Model\Info $vendor,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->transaction = $transaction;
        $this->convertOrder = $convertOrder;
        $this->vendor = $vendor;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        $allowedOrderStatuses = $this->scopeConfig
            ->getValue(\Mageinn\Dropship\Model\Info::CONFIGURATION_OPTION_DEFAULT_DROPSHIP_ORDER_STATUS);

        if (!$order->getId()
            || !$order->canShip()
            || !in_array($order->getStatus(), explode(',', $allowedOrderStatuses))
        ) {
            return;
        }

        $vendorItems = [];
        foreach ($order->getAllItems() as $item) {
            $vendorItems[$item->getVendorId()][$item->getId()] = $item;
        }


        foreach ($vendorItems as $key => $vendorItem) {
            try {
                $shipment = $this->convertOrder->toShipment($order);
                $shipment->setVendorId($key);
                $totalQty = 0;
                foreach ($vendorItem as $item) {
                    $qtyShipped = $item->getQtyToShip();
                    $totalQty += $qtyShipped;
                    $shipmentItem = $this->convertOrder->itemToShipmentItem($item)->setQty($qtyShipped);
                    $shipmentItem->setVendorId($key);
                    $shipment->addItem($shipmentItem);
                }
                if ($shipment) {
                    $vendor = $this->vendor->load($key);
                    $shipmentStatus = $vendor->getVendorShipmentStatus();
                    $shipment->setDropshipStatus($shipmentStatus);
                    $shipment->setTotalQty($totalQty);
                    $shipment->getOrder()->setIsInProcess(true);
                    $this->transaction->addObject($shipment)->addObject($shipment->getOrder())->save();
                }
            } catch (\Exception $e) {
                $order->addStatusHistoryComment('Error: ' . $e->getMessage(), false);
                $order->save();
            }
        }
    }
}
