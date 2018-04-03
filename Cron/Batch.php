<?php
namespace Mageinn\Vendor\Cron;

use \Mageinn\Vendor\Model\Source\BatchStatus;

use \Magento\Framework\Stdlib\DateTime\DateTime;
use \Magento\Cron\Observer\ProcessCronQueueObserver;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Psr\Log\LoggerInterface;

/**
 * Class Batch
 *
 * @package Mageinn\Vendor\Cron
 */
class Batch
{
    /** Schedule ahead group id. */
    const SCHEDULE_CRON_GROUP_ID = 'default';

    /**
     * @var \Mageinn\Vendor\Model\Batch
     */
    protected $batchFactory;

    /**
     * @var array
     */
    protected $scheduledBatches = [];

    /**
     * @var \Mageinn\Vendor\Model\InfoFactory
     */
    protected $vendorFactory;

    /**
     * @var \Mageinn\Vendor\Model\ResourceModel\Info\Collection
     */
    protected $batchEnabledVendors;

    /**
     * @var array
     */
    protected $alreadyScheduled = [];

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Schedule ahead time.
     * @var int
     */
    protected $scheduleAhead;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Mageinn\Core\Helper\Data
     */
    protected $helper;

    /**
     * Batch constructor.
     *
     * @param \Mageinn\Vendor\Model\BatchFactory $batchFactory
     * @param \Mageinn\Vendor\Model\InfoFactory $vendorFactory
     * @param DateTime $dateTime
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param \Mageinn\Core\Helper\Data $helper
     */
    public function __construct(
        \Mageinn\Vendor\Model\BatchFactory $batchFactory,
        \Mageinn\Vendor\Model\InfoFactory $vendorFactory,
        DateTime $dateTime,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        \Mageinn\Core\Helper\Data $helper
    ) {
        $this->batchFactory = $batchFactory;
        $this->vendorFactory = $vendorFactory;
        $this->dateTime = $dateTime;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * Schedule vendor batch cron.
     */
    public function execute()
    {
        $this->scheduleAhead = $this->_getScheduleTimeInterval();
        $scheduledBatches = $this->_getScheduledBatches();
        $vendors = $this->_getBatchEnabledVendors();
        $currentTime = $this->dateTime->gmtTimestamp();

        foreach ($vendors as $vendor) {
            if ($vendor->getBatchImportEnabled()) {
                $this->_createBatch(
                    $vendor->getId(),
                    $vendor->getBatchImportSchedule(),
                    \Mageinn\Vendor\Model\Source\BatchType::MAGEINN_VENDOR_BATCH_TYPE_IMPORT,
                    $currentTime,
                    $vendor->getBatchImportSource()
                );
            }
            if ($vendor->getBatchExportEnabled()) {
                $this->_createBatch(
                    $vendor->getId(),
                    $vendor->getBatchExportSchedule(),
                    \Mageinn\Vendor\Model\Source\BatchType::MAGEINN_VENDOR_BATCH_TYPE_EXPORT,
                    $currentTime
                );
            }
        }

        $this->_runJobs($scheduledBatches, $currentTime);
    }

    /**
     * Get scheduled ahead time.
     *
     * @return int
     */
    protected function _getScheduleTimeInterval()
    {
        $scheduleAheadFor = (int)$this->scopeConfig->getValue(
            'system/cron/' . self::SCHEDULE_CRON_GROUP_ID . '/' . ProcessCronQueueObserver::XML_PATH_SCHEDULE_AHEAD_FOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $scheduleAheadFor = $scheduleAheadFor * ProcessCronQueueObserver::SECONDS_IN_MINUTE;

        return $scheduleAheadFor;
    }

    /**
     * Get currently scheduled batches.
     *
     * @return array
     */
    protected function _getScheduledBatches()
    {
        if (!$this->scheduledBatches) {
            $this->scheduledBatches = $this->batchFactory->create()->getCollection()->addFieldToFilter(
                'status',
                BatchStatus::BATCH_STATUS_SCHEDULED
            );
        }

        return $this->scheduledBatches;
    }

    /**
     * Returns the vendors with at least one of the batches enabled
     *
     * @return \Mageinn\Vendor\Model\ResourceModel\Info\Collection
     */
    protected function _getBatchEnabledVendors()
    {
        if (!$this->batchEnabledVendors) {
            $this->batchEnabledVendors = $this->vendorFactory->create()->getCollection()->addFieldToFilter(
                [
                    'batch_import_enabled',
                    'batch_export_enabled',
                ],
                [
                    ['eq' => '1'],
                    ['eq' => '1'],
                ]
            );
        }

        return $this->batchEnabledVendors;
    }

    /**
     * Creates a batch and saves it
     *
     * @param $vendorId
     * @param $cronExpression
     * @param $type
     * @param $currentTime
     * @param null $source
     */
    protected function _createBatch($vendorId, $cronExpression, $type, $currentTime, $source = null)
    {
        $timeAhead = $currentTime + $this->scheduleAhead;
        for ($time = $currentTime; $time < $timeAhead; $time += ProcessCronQueueObserver::SECONDS_IN_MINUTE) {
            $scheduledAt = strftime('%Y-%m-%d %H:%M:00', $time);

            if ($this->_notAlreadyScheduled($type . '/' . $scheduledAt . '/' . $vendorId)) {
                $this->_saveBatchSchedule($cronExpression, $scheduledAt, $vendorId, $type, $source);
            }
        }
    }

    /**
     * @param $scheduleKey
     *
     * @return bool
     */
    protected function _notAlreadyScheduled($scheduleKey)
    {
        if (!$this->alreadyScheduled) {
            foreach ($this->_getScheduledBatches() as $batch) {
                $key = $batch->getType() . '/' . $batch->getScheduledAt() . '/' . $batch->getVendorId();
                $this->alreadyScheduled[$key] = 1;
            }
        }

        return empty($this->alreadyScheduled[$scheduleKey]);
    }

    /**
     * Save scheduled batch.
     *
     * @param $cronExpression
     * @param $scheduledAt
     * @param $vendorId
     * @param $type
     * @param $source
     */
    protected function _saveBatchSchedule($cronExpression, $scheduledAt, $vendorId, $type, $source)
    {
        /** @var \Mageinn\Vendor\Model\Batch $batch */
        $batch = $this->batchFactory->create();

        try {
            $batch->setCronExpr($cronExpression)->setScheduledAt($scheduledAt);
            if ($batch->trySchedule()) {
                $batch->setVendorId($vendorId)
                    ->setType($type)
                    ->setStatus(BatchStatus::BATCH_STATUS_SCHEDULED)
                    ->setCreatedAt(strftime('%Y-%m-%d %H:%M:%S', $this->dateTime->gmtTimestamp()))
                    ->setFilePath($source)
                    ->save();
            }
        } catch (\Exception $ex) {
            $this->logger->warning($ex);
            $batch->setStatus(BatchStatus::BATCH_STATUS_ERROR);
            $batch->setErrorInfo($ex->getMessage())->save();
        }
    }

    /**
     * Run scheduled jobs.
     *
     * @param $scheduledBatches
     * @param $currentTime
     */
    protected function _runJobs($scheduledBatches, $currentTime)
    {
        // @codingStandardsIgnoreStart
        /** @var \Mageinn\Vendor\Model\Batch $batch */
        foreach ($scheduledBatches as $batch) {
            $scheduledTime = strtotime($batch->getScheduledAt());
            if ($scheduledTime > $currentTime) {
                continue;
            }

            try {
                if ($batch->tryLockJob()) {
                    $batch->runJob()->save();
                }
            } catch (\Exception $ex) {
                $this->logger
                    ->warning('Error for vendor with ID: ' . $batch->getVendorId() . ' -> ' . $ex->getMessage());
                $batch->setStatus(BatchStatus::BATCH_STATUS_ERROR);
                $batch->setErrorInfo($ex->getMessage())->save();
            }
        }
        // @codingStandardsIgnoreEnd
    }
}
