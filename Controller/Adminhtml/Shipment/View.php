<?php

namespace Mageinn\Dropship\Controller\Adminhtml\Shipment;

use Mageinn\Dropship\Model\Source\ShipmentStatus;

class View extends \Magento\Shipping\Controller\Adminhtml\Order\Shipment\View
{
    public function execute()
    {
        $this->shipmentLoader->setOrderId($this->getRequest()->getParam('order_id'));
        $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
        $this->shipmentLoader->setShipment($this->getRequest()->getParam('shipment'));
        $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));

        $shipment = $this->shipmentLoader->load();
        if ($shipment) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getLayout()->getBlock('sales_shipment_view')
                ->updateBackButtonUrl($this->getRequest()->getParam('come_from'));
            $resultPage->setActiveMenu('Magento_Sales::sales_shipment');

            $resultPage->getConfig()->getTitle()
                ->prepend(
                    __(
                        'Shipment #%1 [%2]',
                        $shipment->getIncrementId(),
                        ShipmentStatus::getLabel($shipment->getDropshipStatus())
                    )
                );

            return $resultPage;
        } else {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');

            return $resultForward;
        }
    }
}
