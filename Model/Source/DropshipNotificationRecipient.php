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
namespace Mageinn\Dropship\Model\Source;

use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DropshipNotificationRecipient
 * @package Mageinn\Dropship\Model\Source
 */
class DropshipNotificationRecipient implements OptionSourceInterface
{
    const DROPSHIP_NOTIFICATION_RECIPIENT_VENDOR_EMAIL    = 'vendor_email';
    const DROPSHIP_NOTIFICATION_RECIPIENT_CUSTOMER_SERVICE_EMAIL  = 'customer_service_email';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::DROPSHIP_NOTIFICATION_RECIPIENT_VENDOR_EMAIL,
                'label' => __('Vendor Email')
            ],
            [
                'value' => self::DROPSHIP_NOTIFICATION_RECIPIENT_CUSTOMER_SERVICE_EMAIL,
                'label' => __('Customer service email')
            ],
        ];
    }
}
