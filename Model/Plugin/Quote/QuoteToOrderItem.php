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
namespace Mageinn\Dropship\Model\Plugin\Quote;

use Closure;
use Magento\Framework\DataObject\Copy\Config;

/**
 * Class QuoteToOrderItem
 * @package Mageinn\Dropship\Model\Plugin\Quote
 */
class QuoteToOrderItem
{
    protected $fieldsetName;
    protected $fieldsetConfig;

    /**
     * QuoteToOrderItem constructor.
     * @param Config $fieldsetConfig
     */
    public function __construct(Config $fieldsetConfig)
    {
        $this->fieldsetConfig = $fieldsetConfig;
        $this->fieldsetName = 'quote_convert_item';
    }

    /**
     * @return string
     */
    public function getFieldsetName()
    {
        return $this->fieldsetName;
    }

    /**
     * @return Config
     */
    public function getFieldsetConfig()
    {
        return $this->fieldsetConfig;
    }

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
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);

        $fields = $this->getFieldsetConfig()->getFieldset($this->getFieldsetName(), 'global');

        foreach ($fields as $code => $field) {
            if (!$orderItem->getData($code)) {

                $orderItem->setData($code, $item->getData($code));
            }
        }

        return $orderItem;
    }
}
