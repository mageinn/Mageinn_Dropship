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
namespace Mageinn\Dropship\Cron;

use \Mageinn\Dropship\Model\Source\BatchStatus;

use \Magento\Framework\Stdlib\DateTime\DateTime;
use \Magento\Cron\Observer\ProcessCronQueueObserver;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Psr\Log\LoggerInterface;

/**
 * Class Batch
 * @package Mageinn\Dropship\Cron
 */
class Batch
{
    const SCHEDULE_CRON_GROUP_ID = 'default';

    /**
     * @var \Mageinn\Dropship\Model\BatchFactory
     */
    protected $batchFactory;

    /**
     * @var array
     */
    protected $scheduledBatches = [];

    /**
     * @var \Mageinn\Dropship\Model\InfoFactory
     */
    protected $vendorFactory;

    /**
     * @var \Mageinn\Dropship\Model\ResourceModel\Info\Collection
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
     * @var \Mageinn\Dropship\Helper\CoreData
     */
    protected $helper;

    /**
     * Batch constructor.
     * @param \Mageinn\Dropship\Model\BatchFactory $batchFactory
     * @param \Mageinn\Dropship\Model\InfoFactory $vendorFactory
     * @param DateTime $dateTime
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param \Mageinn\Dropship\Helper\CoreData $helper
     */
    public function __construct(
        \Mageinn\Dropship\Model\BatchFactory $batchFactory,
        \Mageinn\Dropship\Model\InfoFactory $vendorFactory,
        DateTime $dateTime,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        \Mageinn\Dropship\Helper\CoreData $helper
    ) {
        $this->batchFactory = $batchFactory;
        $this->vendorFactory = $vendorFactory;
        $this->dateTime = $dateTime;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * @return void
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
                    \Mageinn\Dropship\Model\Source\BatchType::MAGEINN_DROPSHIP_BATCH_TYPE_IMPORT,
                    $currentTime,
                    $vendor->getBatchImportSource()
                );
            }
            if ($vendor->getBatchExportEnabled()) {
                $this->_createBatch(
                    $vendor->getId(),
                    $vendor->getBatchExportSchedule(),
                    \Mageinn\Dropship\Model\Source\BatchType::MAGEINN_DROPSHIP_BATCH_TYPE_EXPORT,
                    $currentTime
                );
            }
        }

        $this->_runJobs($scheduledBatches, $currentTime);
    }

    /**
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
     * @return array|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
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
     * @return \Mageinn\Dropship\Model\ResourceModel\Info\Collection|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
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
     * @param $cronExpression
     * @param $scheduledAt
     * @param $vendorId
     * @param $type
     * @param $source
     */
    protected function _saveBatchSchedule($cronExpression, $scheduledAt, $vendorId, $type, $source)
    {
        /** @var \Mageinn\Dropship\Model\Batch $batch */
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
     * @param $scheduledBatches
     * @param $currentTime
     */
    protected function _runJobs($scheduledBatches, $currentTime)
    {
        /** @var \Mageinn\Dropship\Model\Batch $batch */
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
    }
}
