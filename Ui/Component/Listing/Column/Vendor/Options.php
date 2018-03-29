<?php

namespace Mageinn\Dropship\Ui\Component\Listing\Column\Vendor;

use Magento\Framework\Data\OptionSourceInterface;
use Mageinn\Dropship\Model\ResourceModel\Info\CollectionFactory;

/**
 * Class Options
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
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get options
     *
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
