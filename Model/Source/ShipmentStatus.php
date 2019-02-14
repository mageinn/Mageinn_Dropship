<?php
namespace Iredeem\Vendor\Model\Source;

use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ShipmentStatus Source.
 *
 * @package Iredeem\Vendor\Model\Source
 */
class ShipmentStatus implements OptionSourceInterface
{
    /**#@+
     * Enabled/Disabled Statuses
     */
    const SHIPMENT_STATUS_PENDING    = 0;
    const SHIPMENT_STATUS_SHIPPED    = 1;
    const SHIPMENT_STATUS_PARTIAL    = 2;
    const SHIPMENT_STATUS_READY      = 3;
    const SHIPMENT_STATUS_ONHOLD     = 4;
    const SHIPMENT_STATUS_BACKORDER  = 5;
    const SHIPMENT_STATUS_CANCELED   = 6;
    const SHIPMENT_STATUS_DELIVERED  = 7;
    const SHIPMENT_STATUS_PENDPICKUP = 8;
    const SHIPMENT_STATUS_ACK        = 9;
    const SHIPMENT_STATUS_EXPORTED   = 10;
    const SHIPMENT_STATUS_RETURNED   = 11;
    const SHIPMENT_STATUS_REFUNDED   = 12;
    const SHIPMENT_STATUS_FRAUD      = 13;
    /**#@-*/

    protected static $statusOptions = [
        self::SHIPMENT_STATUS_PENDING    => 'Pending',
        self::SHIPMENT_STATUS_EXPORTED   => 'Exported',
        self::SHIPMENT_STATUS_ACK        => 'Acknowledged',
        self::SHIPMENT_STATUS_BACKORDER  => 'Backorder',
        self::SHIPMENT_STATUS_ONHOLD     => 'On Hold',
        self::SHIPMENT_STATUS_READY      => 'Ready to Ship',
        self::SHIPMENT_STATUS_PENDPICKUP => 'Pending Pickup',
        self::SHIPMENT_STATUS_PARTIAL    => 'Label(s) printed',
        self::SHIPMENT_STATUS_SHIPPED    => 'Shipped',
        self::SHIPMENT_STATUS_DELIVERED  => 'Delivered',
        self::SHIPMENT_STATUS_CANCELED   => 'Canceled',
        self::SHIPMENT_STATUS_RETURNED   => 'Returned',
        self::SHIPMENT_STATUS_REFUNDED   => 'Refunded',
        self::SHIPMENT_STATUS_FRAUD      => 'Fraud',
    ];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return self::getStatusOptions();
    }

    public static function getStatusOptions()
    {
        $options = [];
        array_walk(self::$statusOptions, function ($label, $value) use (&$options) {
            $options[] = ['value' => $value, 'label' => __($label)];
        });

        return $options;
    }

    /**
     * @param string|int $value
     *
     * @return string
     */
    public static function getLabel($value)
    {
        return __(self::$statusOptions[(int)$value]);
    }
}
