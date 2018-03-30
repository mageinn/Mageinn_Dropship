<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractBatchRows;

use Mageinn\Dropship\Model\ResourceModel\BatchRow\CollectionFactory;

/**
 * Class Grid
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractBatchRows
 */
abstract class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $registry = null;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Helper\Data $backendHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->registry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        $this->setId('iredeem_batch_data_rows');
    }

    /**
     * @return \Mageinn\Dropship\Model\Batch|null
     */
    public function getBatch()
    {
        return $this->registry->registry('iredeem_batch');
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        if ($this->getBatch()->getId()) $this->setDefaultFilter('entity_id');

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('batch_id', ['eq' => $this->getBatch()->getId()]);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sales/batchRows/view', ['_current' => true]);
    }
}
