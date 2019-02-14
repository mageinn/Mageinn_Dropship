<?php
namespace Iredeem\Vendor\Controller\Adminhtml\Shipment;

use Iredeem\Vendor\Model\Source\ShipmentStatus;
use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\View\Result\PageFactory;

class Ship extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::shipment';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;


    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * Edit action constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->shipmentLoader = $shipmentLoader;

        parent::__construct($context);
    }


    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $shipmentId = $this->getRequest()->getParam('shipment_id');
        try {
            if ($shipmentId) {
                $this->shipmentLoader->setShipmentId($shipmentId);
                $shipment = $this->shipmentLoader->load();

                if ($shipment) {
                    $shipment->setDropshipStatus(ShipmentStatus::SHIPMENT_STATUS_SHIPPED);
                    $shipment->addComment('Shipment has been complete.', false, false);
                    $shipment->save();

                    $this->messageManager->addSuccess(__('Successfully marked as shipped!'));

                    return $resultRedirect->setPath('sales/shipment/view', ['_current' => true]);
                } else {
                    $this->messageManager->addError(__('This shipment no longer exists.'));

                    return $resultRedirect->setPath('*/*/');
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $this->messageManager->addError(__('There was a problem marking as shipped.'));

        return $resultRedirect->setPath('*/*/');
    }
}
