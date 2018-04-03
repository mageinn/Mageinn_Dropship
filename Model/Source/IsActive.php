<?php
namespace Mageinn\Vendor\Model\Source;

use \Magento\Framework\Data\OptionSourceInterface;
use \Mageinn\Vendor\Model\Info;

/**
 * Class IsActive Source.
 *
 * @package Mageinn\Vendor\Model\Source
 */
class IsActive implements OptionSourceInterface
{
    /** @var \Mageinn\Vendor\Model\Info */
    protected $vendor;

    public function __construct(Info $vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->vendor->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
