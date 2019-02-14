<?php
namespace Iredeem\Vendor\Model\Source;

use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DropshipNotificationRecipient Source.
 *
 * @package Iredeem\Vendor\Model\Source
 */
class DropshipNotificationRecipient implements OptionSourceInterface
{
    /**
     * Batch statuses
     */
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
