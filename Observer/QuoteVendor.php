<?php
namespace Mageinn\Vendor\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Quote Vendor ID Observer
 *
 * @package Mageinn\Vendor\Observer
 */
class QuoteVendor implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        $quoteItem = $observer->getQuoteItem();
        // fix missing vendor id on checkout
        if (!$quoteItem->getVendorId()) {
            $quoteItem->setVendorId($product->getVendorId());
        }
    }
}
