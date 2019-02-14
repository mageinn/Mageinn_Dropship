<?php
namespace Mageinn\Dropship\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Quote Vendor ID Observer
 *
 * @package Mageinn\Dropship\Observer
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
