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
use Mageinn\Dropship\Model\Info;
use Mageinn\Dropship\Model\Source\DropshipNotificationRecipient;
use Mageinn\Dropship\Model\ResourceModel\Address\CollectionFactory;
use Mageinn\Dropship\Model\Address;
use \Psr\Log\LoggerInterface;

/**
 * Class ShipmentEmail
 * @package Mageinn\Dropship\Observer
 */
class ShipmentEmail implements ObserverInterface
{
    /**
     * @var \Mageinn\Dropship\Model\Info
     */
    protected $vendor;

    /**
     * @var \Mageinn\Dropship\Model\ResourceModel\Address\Collection;
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
     * @param \Mageinn\Dropship\Magento\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Escaper $escaper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Info $vendor,
        CollectionFactory $vendorAddress,
        \Mageinn\Dropship\Magento\Mail\Template\TransportBuilder $transportBuilder,
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

        if (!$vendor->getNotifyOrder()) {
            return;
        }

        $vendorEmailTemplate =
            $this->scopeConfig->getValue(Info::CONFIGURATION_NOTIFICATION_EMAIL_TEMPLATE);

        $sender = $this->scopeConfig->getValue(Info::CONFIGURATION_NOTIFICATION_SENDER);
        $recipient = $vendor->getEmail();
        $ccRecipient = $vendor->getNotifyOrderEmail();

        if ($this->scopeConfig->getValue(Info::CONFIGURATION_NOTIFICATION_RECIPIENT) ==
                DropshipNotificationRecipient::DROPSHIP_NOTIFICATION_RECIPIENT_CUSTOMER_SERVICE_EMAIL) {
            $vendorAddressEmail = $this->vendorAddress->create()
                ->addFieldToFilter('vendor_id', ['eq' => $shipment->getVendorId()])
                ->addFieldToFilter('type', ['eq' => Address::ADDRESS_TYPE_CUSTOMER_SERVICE])
                ->getFirstItem()
                ->getEmail();
            if ($vendorAddressEmail) {
                $recipient = $vendorAddressEmail;
            }
        }

        $vars = [
            'shipment' => $shipment,
            'order' => $order
        ];

        $vars = new \Magento\Framework\DataObject($vars);

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
