<?php

namespace Iredeem\Vendor\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Iredeem\Vendor\Model\Info;
use Iredeem\Vendor\Model\Source\DropshipNotificationRecipient;
use Iredeem\Vendor\Model\ResourceModel\Address\CollectionFactory;
use Iredeem\Vendor\Model\Address;
use \Psr\Log\LoggerInterface;

class ShipmentEmail implements ObserverInterface
{
    /**
     * @var \Iredeem\Vendor\Model\Info
     */
    protected $vendor;

    /**
     * @var \Iredeem\Vendor\Model\ResourceModel\Address\Collection;
     */
    protected $vendorAddress;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * ShipmentEmail constructor.
     * @param Info $vendor
     * @param CollectionFactory $vendorAddress
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Escaper $escaper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Info $vendor,
        CollectionFactory $vendorAddress,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        LoggerInterface $logger
    ) {
        $this->vendor = $vendor;
        $this->vendorAddress = $vendorAddress;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();
        $vendor = $this->vendor->load($shipment->getVendorId());

        /* Check if vendor notification for new orders is enabled */
        if (!$vendor->getNotifyOrder()) {
            return;
        }

        $vendorEmailTemplate =
            $this->scopeConfig->getValue(Info::CONFIGURATION_NOTIFICATION_EMAIL_TEMPLATE);

        $sender = $this->scopeConfig->getValue(Info::CONFIGURATION_NOTIFICATION_SENDER);
        $recipient = $vendor->getEmail();
        $ccRecipient = $vendor->getNotifyOrderEmail();

        /* Check to what email recipient to send vendor email */
        if ($this->scopeConfig->getValue(Info::CONFIGURATION_NOTIFICATION_RECIPIENT) ==
                DropshipNotificationRecipient::DROPSHIP_NOTIFICATION_RECIPIENT_CUSTOMER_SERVICE_EMAIL) {
            $vendorAddressEmail = $this->vendorAddress->create()
                ->addFieldToFilter('vendor_id', ['eq' => $shipment->getVendorId()])
                ->addFieldToFilter('type', ['eq' => Address::ADDRESS_TYPE_CUSTOMER_SERVICE])
                ->getFirstItem()
                ->getEmail();
            /* If no vendor customer email is set will send to default vendor email */
            if ($vendorAddressEmail) {
                $recipient = $vendorAddressEmail;
            }
        }

        $vars = [
            'shipment' => $shipment,
            'order' => $order
        ];

        // @codingStandardsIgnoreStart
        $vars = new \Magento\Framework\DataObject($vars);
        // @codingStandardsIgnoreEnd

        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($vendorEmailTemplate)
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId()])
                ->setTemplateVars($vars->getData())
                ->setFrom($sender)
                ->addTo($recipient)
                ->addCc($ccRecipient)
                ->getTransport();

            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->warning($e);
        }
    }
}
