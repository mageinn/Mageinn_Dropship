<?php
namespace Mageinn\Dropship\Model\ResourceModel\Batch\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Psr\Log\LoggerInterface as Logger;
use Mageinn\Dropship\Model\Batch;

/**
 * Flat batches grid collection
 *
 * @package Mageinn\Dropship\Model\ResourceModel\Info\Grid
 */
class Collection extends SearchResult
{
    /**
     * @var \Mageinn\Dropship\Model\Batch
     */
    protected $batchModel;

    /**
     * Collection constructor.
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param Batch $batchModel
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        Batch $batchModel,
        $mainTable,
        $resourceModel
    ) {
        $this->batchModel = $batchModel;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }
}
