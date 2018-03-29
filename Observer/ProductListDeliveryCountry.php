<?php
namespace Mageinn\Dropship\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ProductListDeliveryCountry
 * @package Mageinn\Dropship\Observer
 */
class ProductListDeliveryCountry implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * ProductListDeliveryCountry constructor.
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(\Magento\Framework\App\Http\Context $httpContext)
    {
        $this->httpContext  = $httpContext;
    }

    /**
     * Apply delivery country filter on product collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $deliveryCountry =
            $this->httpContext->getValue(\Mageinn\Dropship\Model\Plugin\DeliveryCountry::COOKIE_DELIVERY);

        if ($deliveryCountry) {
            $productCollection = $observer->getCollection();
            $productCollection->addAttributeToFilter([[
                'attribute' => 'delivery_country',
                'finset' => $deliveryCountry
            ]]);
        }

        return $this;
    }
}
