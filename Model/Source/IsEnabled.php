<?php
namespace Mageinn\Dropship\Model\Source;

use \Magento\Framework\Data\OptionSourceInterface;
use \Mageinn\Dropship\Model\Info;

/**
 * Class IsEnabled Source.
 *
 * @package Mageinn\Dropship\Model\Source
 */
class IsEnabled implements OptionSourceInterface
{
    /** @var \Mageinn\Dropship\Model\Info */
    protected $vendor;

    /**
     * IsEnabled constructor.
     *
     * @param \Mageinn\Dropship\Model\Info $vendor
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
