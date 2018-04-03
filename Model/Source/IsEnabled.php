<?php
namespace Mageinn\Vendor\Model\Source;

use \Magento\Framework\Data\OptionSourceInterface;
use \Mageinn\Vendor\Model\Info;

/**
 * Class IsEnabled Source.
 *
 * @package Mageinn\Vendor\Model\Source
 */
class IsEnabled implements OptionSourceInterface
{
    /** @var \Mageinn\Vendor\Model\Info */
    protected $vendor;

    /**
     * IsEnabled constructor.
     *
     * @param \Mageinn\Vendor\Model\Info $vendor
     */
    public function __construct(Info $vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->vendor->getAvailableFlags();
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
