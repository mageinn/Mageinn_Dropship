<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractFilePath;

use Mageinn\Dropship\Model\Source\BatchStatus;
use Magento\Backend\Helper\Data;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Registry;

/**
 * Class Grid
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractFilePath
 */
abstract class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var Registry|null
     */
    protected $registry = null;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var BatchStatus
     */
    protected $batchStatus;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Data $backendHelper
     * @param Registry $registry
     * @param CollectionFactory $collectionFactory
     * @param BatchStatus $batchStatus
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Data $backendHelper,
        Registry $registry,
        CollectionFactory $collectionFactory,
        BatchStatus $batchStatus,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->registry = $registry;
        $this->batchStatus = $batchStatus;
        $this->logger = $logger;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('iredeem_batch_file_path');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setFilterVisibility(false);
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

        try {
            $collection = $this->collectionFactory->create()->addItem($this->getBatch());
            $this->setCollection($collection);
        } catch (\Exception $exception) {
            $this->logger->error($exception);
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
