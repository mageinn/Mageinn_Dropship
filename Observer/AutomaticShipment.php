<?php
namespace Mageinn\Vendor\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AutomaticShipment implements ObserverInterface
{
    /** @var \Magento\Sales\Model\Order\ShipmentDocumentFactory  */
    protected $shipmentFactory;

    /** @var \Magento\Framework\DB\Transaction  */
    protected $transaction;

    /** @var \Magento\Sales\Model\Convert\Order  */
    protected $convertOrder;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface  */
    protected $vendor;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface  */
    protected $scopeConfig;

    public function __construct(
        \Magento\Sales\Model\Order\ShipmentDocumentFactory $shipmentFactory,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Mageinn\Vendor\Model\Info $vendor,
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
     * @return $this|void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        $allowedOrderStatuses = $this->scopeConfig
            ->getValue(\Mageinn\Vendor\Model\Info::CONFIGURATION_OPTION_DEFAULT_DROPSHIP_ORDER_STATUS);

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

        // @codingStandardsIgnoreStart
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
