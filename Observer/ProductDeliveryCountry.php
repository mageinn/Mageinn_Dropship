<?php

namespace Mageinn\Vendor\Observer;

use Mageinn\Vendor\Model\ShippingRate;
use Mageinn\Vendor\Model\Region;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductDeliveryCountry implements ObserverInterface
{
    /**
     * @var \Mageinn\Vendor\Model\ResourceModel\Region\CollectionFactory
     */
    protected $regionsFactory;

    /**
     * @var \Mageinn\Vendor\Model\ResourceModel\ShippingRate\CollectionFactory $ratesFactory
     */
    protected $ratesFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * ProductDeliveryCountry constructor.
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Mageinn\Vendor\Model\ResourceModel\Region\CollectionFactory $regionsFactory
     * @param \Mageinn\Vendor\Model\ResourceModel\ShippingRate\CollectionFactory $ratesFactory
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Mageinn\Vendor\Model\ResourceModel\Region\CollectionFactory $regionsFactory,
        \Mageinn\Vendor\Model\ResourceModel\ShippingRate\CollectionFactory $ratesFactory
    ) {
        $this->regionsFactory = $regionsFactory;
        $this->ratesFactory = $ratesFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        // Case no shipping rates applyed.
        if (!$product->getShippingRuleset()) {
            if (!(bool)$product->getDeliveryCountryOverride()) {
                $product->setDeliveryCountry(null);
            }

            return;
        }

        // Case apply shipping rates.
        $ratesCollection = $this->getVendorShippingRatesCollection($product);
        $countries = $ratesCollection->getColumnValues(ShippingRate::SHIPPING_RATE_DATA_COUNTRY);

        if ((bool)$product->getDeliveryCountryOverride()) {
            $remainingCountries = array_diff($product->getDeliveryCountry(), $countries);
            $countries = array_intersect($product->getDeliveryCountry(), $countries);

            $remainingCountries = $this->getCountryNamesByCode($remainingCountries);
            if (!empty($remainingCountries)) {
                $this->messageManager->addNoticeMessage(__(
                    'Countries (%1) not saved as delivery countries since there\'s no specific shipping rate imported!',
                    implode(',', $remainingCountries)
                ));
            }
        }

        $product->setDeliveryCountry($countries);
        $this->applyShippingRates($product);
    }

    /**
     * @param $product
     * @return \Mageinn\Vendor\Model\ResourceModel\ShippingRate\Collection
     */
    private function getVendorShippingRatesCollection($product)
    {
        $ratesCollection = $this->ratesFactory->create();

        $ratesCollection->getSelect()
            ->columns([ShippingRate::SHIPPING_RATE_DATA_COUNTRY])
            ->where(
                ShippingRate::SHIPPING_RATE_DATA_VENDOR_ID.' = ?',
                $product->getVendorId()
            )->where(
                ShippingRate::SHIPPING_RATE_DATA_GROUP.' = ?',
                $product->getShippingRuleset()
            );

        return $ratesCollection;
    }

    /**
     * @param array $countries
     * @return array
     */
    private function getCountryNamesByCode($countries)
    {
        $collection = $this->regionsFactory->create()
            ->addFieldToSelect(Region::REGION_DATA_COUNTRY)
            ->addFieldToFilter(Region::REGION_DATA_CODE, ['in' => $countries]);

        return $collection->getColumnValues(Region::REGION_DATA_COUNTRY);
    }

    /**
     * @param $product
     * @return bool
     */
    private function applyShippingRates($product)
    {
        $product->getId();

        // this needs to be implemented, currently added as a ref.
        return true;
    }
}
