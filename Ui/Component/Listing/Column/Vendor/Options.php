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
namespace Mageinn\Dropship\Ui\Component\Listing\Column\Vendor;

use Magento\Framework\Data\OptionSourceInterface;
use Mageinn\Dropship\Model\ResourceModel\Info\CollectionFactory;

/**
 * Class Options
 * @package Mageinn\Dropship\Ui\Component\Listing\Column\Vendor
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Options constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $options = $this->options = $this->collectionFactory->create()->toOptionArray();
            usort($options, function ($a, $b) {
                return strcmp(strtolower($a["label"]), strtolower($b["label"]));
            });
            $this->options = $options;
        }
        return $this->options;
    }
}
