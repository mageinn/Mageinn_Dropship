<?php
namespace Iredeem\Vendor\Model\Source;

use \Magento\Framework\Data\OptionSourceInterface;
use \Iredeem\Vendor\Model\Info;

/**
 * Class IsEnabled Source.
 *
 * @package Iredeem\Vendor\Model\Source
 */
class IsEnabled implements OptionSourceInterface
{
    /** @var \Iredeem\Vendor\Model\Info */
    protected $vendor;

    /**
     * IsEnabled constructor.
     *
     * @param \Iredeem\Vendor\Model\Info $vendor
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
