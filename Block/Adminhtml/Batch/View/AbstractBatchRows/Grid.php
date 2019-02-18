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
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractBatchRows;

use Mageinn\Dropship\Model\ResourceModel\BatchRow\CollectionFactory;

/**
 * Class Grid
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractBatchRows
 */
abstract class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Backend\Block\Template\Context $context,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('mageinn_batch_data_rows');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return \Mageinn\Dropship\Model\Batch|null
     */
    public function getBatch()
    {
        return $this->_registry->registry('mageinn_batch');
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        if ($this->getBatch()->getId()) {
            $this->setDefaultFilter('entity_id');
        }

        $collection = $this->_collectionFactory->create()
            ->addFieldToFilter('batch_id', ['eq' => $this->getBatch()->getId()]);

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
