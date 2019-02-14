<?php
namespace Mageinn\Dropship\Model\Batch\Export;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;

/**
 * Class File
 * @package Mageinn\Dropship\Model\Batch\Export
 */
class File extends \Magento\Framework\Filesystem\Io\File
{
    const BATCH_EXPORT_AVAILABLE_DIR_VAR = 'var';
    const BATCH_EXPORT_AVAILABLE_DIR_MEDIA = 'media';

    /**
     * @var array
     */
    protected $datePlaceholders = [
        '[YYYY]',
        '[YY]',
        '[MM]',
        '[DD]',
        '[hh]',
        '[mm]',
        '[ss]',
    ];

    /**
     * @var array
     */
    protected $dateValues = [];

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * @var bool
     */
    protected $emailSentToVendor;

    /**
     * @var \Mageinn\Dropship\Magento\Mail\Template\TransportBuilder 
     */
    protected $transportBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Mageinn\Dropship\Model\Batch\Export\File\Email
     */
    protected $email;

    /**
     * @var array
     */
    protected $notes = [];

    /**
     * @var \Mageinn\Dropship\Model\Batch\Export\File\Response
     */
    protected $response;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $fileInfo;

    /**
     * @var \Mageinn\Dropship\Helper\Data
     */
    protected $transfer;

    /**
     * File constructor.
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Mageinn\Dropship\Magento\Mail\Template\TransportBuilder $builder
     * @param \Psr\Log\LoggerInterface $logger
     * @param File\Email $email
     * @param File\Response $response
     * @param \Magento\Framework\Filesystem\Io\File $fileSystem
     * @param \Mageinn\Dropship\Model\Batch\Handler\Transfer $transfer
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Mageinn\Dropship\Magento\Mail\Template\TransportBuilder $builder,
        \Psr\Log\LoggerInterface $logger,
        \Mageinn\Dropship\Model\Batch\Export\File\Email $email,
        \Mageinn\Dropship\Model\Batch\Export\File\Response $response,
        \Magento\Framework\Filesystem\Io\File $fileSystem,
        \Mageinn\Dropship\Model\Batch\Handler\Transfer $transfer
    ) {
        $this->dir = $dir;
        $this->transportBuilder = $builder;
        $this->logger = $logger;
        $this->email = $email;
        $this->response = $response;
        $this->fileInfo = $fileSystem;
        $this->transfer = $transfer;
    }

    /**
     * Creates the export file
     *
     * @param $contents
     * @param $vendor
     * @return \Mageinn\Dropship\Model\Batch\Export\File\Response
     * @throws \Exception
     */
    public function handle($contents, $vendor)
    {
        $this->emailSentToVendor = false;
        $this->notes = [];
        $uniqueDestinations = explode(PHP_EOL, $vendor->getBatchExportDestination());
        foreach ($uniqueDestinations as $destination) {
            $path = $this->getDestinationPath($destination);
            $isMail = $this->_isMail($path);
            $isSftp = $this->transfer->isSftp($path);
            if ($isSftp) {
                $this->transfer->createFileOnRemoteServer($isSftp, $vendor->getBatchExportPrivateKey(), $contents);
            } elseif ($isMail) {
                $this->_sendEmail($vendor, $contents, $isMail);
            } else {
                $this->_createFileOnServer($contents, $path);
            }
        }
        // If the vendor email was not among the emails in the destination setting, send the email to the vendor
        if (!$this->emailSentToVendor) {
            $this->_sendEmail($vendor, $contents);
        }
        // If there are notes from parsing the email settings, we add them to the current ones
        if ($this->email->getNotes()) {
            $this->notes[] = $this->email->getNotes();
            $this->email->clearNotes();
        }
        $this->response->setNotes($this->notes);

        return $this->response;
    }

    /**
     * Replace the placeholders with the date values
     *
     * @param string $destination
     * @return string
     */
    protected function getDestinationPath($destination)
    {
        $this->dateValues = [
            date('Y'),
            date('y'),
            date('m'),
            date('d'),
            date('h'),
            date('i'),
            date('s'),
        ];

        return str_replace($this->datePlaceholders, $this->dateValues, $destination);
    }

    /**
     * Checks if the destination given is an email
     *
     * @param $destination
     * @return bool
     */
    protected function _isMail($destination)
    {
        if (preg_match('#^mailto:([^?]+)(.*)$#', $destination, $results)) {
            return $results;
        }

        return false;
    }

    /**
     * Creates the file at the specified path
     *
     * @param $contents
     * @param $destination
     * @throws \Exception
     */
    protected function _createFileOnServer($contents, $destination)
    {
        // If the folder is not in media or var, the file will not be created
        if ((strpos($destination, self::BATCH_EXPORT_AVAILABLE_DIR_MEDIA) === 0)
            || (strpos($destination, self::BATCH_EXPORT_AVAILABLE_DIR_VAR) === 0)
        ) {
            $pathInfo = $this->fileInfo->getPathInfo($destination);
            // Get the absolute path of the destination folder on the server
            $exportPath = $this->dir->getRoot() . DIRECTORY_SEPARATOR . $pathInfo['dirname'];

            $this->fileInfo->checkAndCreateFolder($exportPath, 0777);

            $this->open(['path' => $exportPath]);
            $fileName = trim($pathInfo['basename']);
            $this->write($fileName, $contents, 0777);
            $this->response->setFilePath($filePath = $exportPath . DIRECTORY_SEPARATOR . $fileName);
        } else {
            throw new LocalizedException(__('Invalid file destination'));
        }
    }

    /**
     * Sends email based on the setting
     *
     * @param $vendor
     * @param $contents
     * @param null $isMail
     */
    protected function _sendEmail($vendor, $contents, $isMail = null)
    {
        // If there is no email setting parsed from the configuration, the email will be sent to the vendor
        if (!$isMail) {
            try {
                /** @var \Magento\Framework\Mail\TransportInterface $transport */
                $transport = $this->email->prepareEmailForVendor($vendor, $contents, $this->transportBuilder);
                // If the recipient is not set properly, the email will not be sent
                if ($transport) {
                    $transport->sendMessage();
                }
            } catch (\Exception $e) {
                $this->logger->warning($e);
            }
        // If the email components are parsed correctly, then they are processed and the email is sent
        } elseif (count($isMail) == 3) {
            $componentsArray = $this->_getComponentsArray($isMail[2]);
            try {
                $transport = $this->email
                    ->prepareGeneralEmail($isMail[1], $componentsArray, $contents, $this->transportBuilder);
                // If the recipient is not set properly, the email will not be sent
                if ($transport) {
                    $transport->sendMessage();
                }
            } catch (\Exception $e) {
                $this->logger->warning($e);
            }
            if ($isMail[1] == $vendor->getEmail()) {
                $this->emailSentToVendor = true;
            }
        // If the email components were not set properly, a notice is added
        } else {
            $this->notes[] = sprintf('"%s" is not properly set', $isMail[0]);
        }
    }

    /**
     * Returns settings like possible subject or email body from destination setting as an array
     * with the keys being the setting names
     *
     * @param $componentsString
     * @return mixed
     */
    protected function _getComponentsArray($componentsString)
    {
        if ($componentsString[0] == '?') {
            $componentsString = substr($componentsString, 1);
        }

        // @codingStandardsIgnoreStart
        parse_str($componentsString, $componentsArray);
        // @codingStandardsIgnoreEnd

        return $componentsArray;
    }
}
