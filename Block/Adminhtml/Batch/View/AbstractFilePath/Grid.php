<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractFilePath;

/**
 * Class Grid
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractFilePath
 */
abstract class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Mageinn\Dropship\Model\Source\BatchStatus
     */
    protected $_batchStatus;

    /**
     * User constructor.
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Mageinn\Dropship\Model\Source\BatchStatus $batchStatus
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $logger,
        \Mageinn\Dropship\Model\Source\BatchStatus $batchStatus,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_collectionFactory = $collectionFactory;
        $this->_logger = $logger;
        $this->_batchStatus = $batchStatus;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('iredeem_batch_file_path');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
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

        try {
            $collection = $this->_collectionFactory->create()->addItem($this->getBatch());
            $this->setCollection($collection);
        } catch (\Exception $e) {
            $this->_logger->error($e);
        }

        return parent::_prepareCollection();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sales/filePath/view', ['_current' => true]);
    }
}
