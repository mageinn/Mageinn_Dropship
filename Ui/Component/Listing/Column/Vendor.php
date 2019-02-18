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
namespace Mageinn\Dropship\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Vendor
 * @package Mageinn\Dropship\Ui\Component\Listing\Column
 */
class Vendor extends Column
{
    /** @var \Mageinn\Dropship\Model\Info  */
    protected $vendor;

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
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $vendorName = $this->vendor->load($item['vendor_id'])->getName();
                $item['vendor_id'] = $vendorName;
            }
        }
        return $dataSource;
    }
}
