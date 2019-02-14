<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Iredeem\Vendor\Controller\Adminhtml\Shipment;

use Magento\Sales\Model\Order\Email\Sender\ShipmentCommentSender;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\LayoutFactory;

class AddComment extends \Magento\Shipping\Controller\Adminhtml\Order\Shipment\AddComment
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::shipment';

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var ShipmentCommentSender
     */
    protected $shipmentCommentSender;

    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Add comment to shipment history
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->getRequest()->setParam('shipment_id', $this->getRequest()->getParam('id'));
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please enter a comment.')
                );
            }
            $this->shipmentLoader->setOrderId($this->getRequest()->getParam('order_id'));
            $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
            $this->shipmentLoader->setShipment($this->getRequest()->getParam('shipment'));
            $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));

            $shipment = $this->shipmentLoader->load();
            $status = $this->getRequest()->getPost('shipment_status');

            if (!empty($this->_auth->getUser()->getAssocVendorId()) && ($status)
                && (int) $shipment->getDropshipStatus()
                !== \Iredeem\Vendor\Model\Source\ShipmentStatus::SHIPMENT_STATUS_SHIPPED
            ) {
                $shipment->setDropshipStatus($status);
            } elseif (empty($this->_auth->getUser()->getAssocVendorId()) && $status) {
                $shipment->setDropshipStatus($status);
            }

            $shipment->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );

            $this->shipmentCommentSender->send($shipment, !empty($data['is_customer_notified']), $data['comment']);
            $shipment->save();
            $resultLayout = $this->resultLayoutFactory->create();
            $resultLayout->addDefaultHandle();
            $response = $resultLayout->getLayout()->getBlock('shipment_comments')->toHtml();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('Cannot add new comment.')];
        }
        if (is_array($response)) {
            $response = $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($response);
            $this->getResponse()->representJson($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
