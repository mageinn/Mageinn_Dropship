<?php
namespace Mageinn\Dropship\Ui\Component\Listing\Column\Batches;

/**
 * Class Actions
 * @package Mageinn\Dropship\Ui\Component\Listing\Column
 */
class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
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
            foreach ($dataSource['data']['items'] as &$item) {
                // Form the view action based on the batch type so that we can create
                // separate pages for each type of batch
                if ($item['type'] == \Mageinn\Dropship\Model\Source\BatchType::IREDEEM_VENDOR_BATCH_TYPE_IMPORT) {
                    $actionType = 'Import';
                } else {
                    $actionType = 'Export';
                }
                $item[$this->getData('name')]['view'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'sales/batches/view' . $actionType,
                        ['id' => $item['entity_id']]
                    ),
                    'label' => __('View'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
