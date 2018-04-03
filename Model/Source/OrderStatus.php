<?php
namespace Mageinn\Vendor\Model\Source;

use \Magento\Sales\Model\Config\Source\Order\Status;

/**
 * Order archive model
 *
 */
class OrderStatus extends Status
{
    /**
     * Retrieve order statuses as options for select
     *
     * @see \Magento\Sales\Model\Config\Source\Order\Status:toOptionArray()
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_shift($options);
        // Remove '--please select--' option
        return $options;
    }
}
