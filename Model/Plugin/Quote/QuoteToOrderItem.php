<?php
namespace Mageinn\Dropship\Model\Plugin\Quote;

use Closure;

class QuoteToOrderItem
{
    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        $subject;
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);
        $orderItem->setVendorId($item->getVendorId());

        return $orderItem;
    }
}
