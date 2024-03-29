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
namespace Mageinn\Dropship\Controller\Adminhtml\Shipment;

use Mageinn\Dropship\Model\Source\ShipmentStatus;

/**
 * Class View
 * @package Mageinn\Dropship\Controller\Adminhtml\Shipment
 */
class View extends \Magento\Shipping\Controller\Adminhtml\Order\Shipment\View
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
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
