<?php
namespace Mageinn\Dropship\Model;

use Mageinn\Dropship\Model\Source\BatchStatus;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Info
 * @package Mageinn\Dropship\Model
 */
class Batch extends \Magento\Cron\Model\Schedule
{
    const BATCH_DATA_INFORMATION = 'batch_data';
    const BATCH_TYPE_VIEW_IMPORT = 'Import';
    const BATCH_TYPE_VIEW_EXPORT = 'Export';

    /**#@+
     * Table
     */
    const TABLE_DROPSHIP_BATCH = 'mageinn_dropship_batch';
    /**#@-*/

    /**
     * @var \Mageinn\Dropship\Model\InfoFactory
     */
    protected $vendor;

    /**
     * @var \Mageinn\Dropship\Model\Batch\Import
     */
    protected $import;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    /**
     * Batch constructor.
     *
     * @var \Mageinn\Dropship\Model\Batch\Export
     */
    protected $export;

    /**
     * @var \Mageinn\Dropship\Model\ResourceModel\BatchRow
     */
    protected $batchRow;

    /**
     * @var \Mageinn\Dropship\Model\Batch\Handler\Transfer
     */
    protected $transfer;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $io;

    /**
     * Batch constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mageinn\Dropship\Model\InfoFactory $vendor
     * @param \Mageinn\Dropship\Model\Batch\Import $import
     * @param \Mageinn\Dropship\Model\Batch\Export $export
     * @param \Mageinn\Dropship\Model\ResourceModel\BatchRow $batchRow
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\App\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param Batch\Handler\Transfer $transfer
     * @param \Magento\Framework\Filesystem\Io\File $io
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Mageinn\Dropship\Model\InfoFactory $vendor,
        \Mageinn\Dropship\Model\Batch\Import $import,
        \Mageinn\Dropship\Model\Batch\Export $export,
        \Mageinn\Dropship\Model\ResourceModel\BatchRow $batchRow,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem\Driver\File $file,
        Batch\Handler\Transfer $transfer,
        \Magento\Framework\Filesystem\Io\File $io,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->vendor = $vendor;
        $this->import = $import;
        $this->date = $date;
        $this->dir = $dir;
        $this->file = $file;
        $this->export = $export;
        $this->batchRow = $batchRow;
        $this->transfer = $transfer;
        $this->io = $io;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Object initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Mageinn\Dropship\Model\ResourceModel\Batch::class);
    }

    /**
     * Lock current running job.
     *
     * @return bool
     * @throws \Exception
     */
    public function tryLockJob()
    {
        if ($this->getStatus() == BatchStatus::BATCH_STATUS_SCHEDULED) {
            $this->setStatus(BatchStatus::BATCH_STATUS_PROCESSING)->save();

            return true;
        }

        return false;
    }

    /**
     * Run Export job
     *
     * @throws \Exception
     */
    protected function _runExport()
    {
        $this->setStatus(BatchStatus::BATCH_STATUS_EXPORTING);

        $vendor = $this->vendor->create()->load($this->getVendorId());
        $response = $this->export->process($vendor, $this->getId());
        $this->setNumRows($response->getRowsNumber());
        if ($response->getRowsNumber() == 0) {
            $this->setStatus(BatchStatus::BATCH_STATUS_EMPTY);
        } else {
            $this->setRowsText($response->getRowsText())
                ->setFilePath($response->getFilePath());
            if ($response->getNotes()) {
                $this->setNotes($response->getNotes());
            }
            $this->batchRow->bulkInsert($response->getBatchRows());
            $this->setStatus(BatchStatus::BATCH_STATUS_SUCCESS);
        }
    }

    /**
     * Run Export job
     *
     * @throws LocalizedException
     */
    protected function _runImport()
    {
        $this->setStatus(BatchStatus::BATCH_STATUS_IMPORTING);
        $vendor = $this->vendor->create()->load($this->getVendorId());

        $source = $this->getFilePath();
        if ($isSftp = $this->transfer->isSftp($source)) {
            $filePath = $this->dir
                    ->getPath(\Magento\Framework\App\Filesystem\DirectoryList::TMP) . DIRECTORY_SEPARATOR . $isSftp[3];
            $pathInfo = $this->io->getPathInfo($filePath);
            $this->file->createDirectory($pathInfo['dirname']);
            $this->transfer->retrieveFileFromRemoteServer($isSftp, $vendor->getBatchExportPrivateKey(), $filePath);
        } else {
            $filePath = $this->dir->getRoot() . DIRECTORY_SEPARATOR . $source;
        }

        $delimiter = $vendor->getBatchImportDelimiter();
        $template = $this->import->validateImportTemplate($vendor->getBatchImportFileData(), $delimiter);
        $fileData = $this->import->getImportData($filePath, $template, $delimiter);
        if ($fileData) {
            $this->import->updateShipmentBatch(
                $fileData,
                $template,
                $this->getId(),
                $this->getVendorId(),
                $this->date->gmtDate()
            );
            $this->batchRow->bulkInsert($this->import->getBatchRows());
//                $this->file->deleteFile($filePath); Check if the file should be deleted
            $this->setNumRows(count($fileData));
            $this->setRowsText(implode(PHP_EOL, array_map(
                function ($el) use ($delimiter) {
                    return implode($delimiter, $el);
                },
                $fileData
            )));
            $this->setFilePath($source);
            if ($this->import->getBatchRowError()) {
                $this->setStatus(BatchStatus::BATCH_STATUS_ERROR);
            } else {
                $this->setStatus(BatchStatus::BATCH_STATUS_SUCCESS);
            }
        } else {
            $this->setStatus(BatchStatus::BATCH_STATUS_EMPTY);
        }
    }

    /**
     * Run batch schedule jobs.
     *
     * @throws LocalizedException
     * @throws \Exception
     */
    public function runJob()
    {
        $this->setUpdatedAt($this->date->gmtDate());
        switch ($this->getType()) {
            case \Mageinn\Dropship\Model\Source\BatchType::IREDEEM_VENDOR_BATCH_TYPE_EXPORT:
                $this->_runExport();
                break;
            case \Mageinn\Dropship\Model\Source\BatchType::IREDEEM_VENDOR_BATCH_TYPE_IMPORT:
                $this->_runImport();
                break;
            default:
                $this->setStatus(BatchStatus::BATCH_STATUS_ERROR);
                throw new LocalizedException(__('Invalid batch type'));
        }

        return $this;
    }
}
