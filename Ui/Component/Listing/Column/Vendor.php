<?php
namespace Mageinn\Dropship\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Vendor extends Column
{
    /** @var \Mageinn\Dropship\Model\Info  */
    protected $_vendor;

    /**
     * Vendor constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Mageinn\Dropship\Model\Info $vendor
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Mageinn\Dropship\Model\Info $vendor,
        array $components = [],
        array $data = []
    ) {
        $this->vendor = $vendor;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                // @codingStandardsIgnoreStart
                $vendorName = $this->vendor->load($item['vendor_id'])->getName();
                // @codingStandardsIgnoreEnd
                $item['vendor_id'] = $vendorName; //Value which you want to display
            }
        }
        return $dataSource;
    }
}
