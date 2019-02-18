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
use \Mageinn\Dropship\Model\Info;

/**
 * Class IsEnabled
 * @package Mageinn\Dropship\Model\Source
 */
class IsEnabled implements OptionSourceInterface
{
    /**
     * @var Info
     */
    protected $vendor;

    /**
     * IsEnabled constructor.
     * @param Info $vendor
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
