<?php
namespace Mageinn\Dropship\Model\Batch\Handler;

use Magento\Framework\Exception\LocalizedException;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;

/**
 * Class Transfer
 * @package Mageinn\Dropship\Model\Batch\Handler
 * @codeCoverageIgnore
 */
class Transfer
{
    protected $activeConnections = [];

    /**
     * Check if the destination is SFTP
     *
     * @param $destination
     * @return bool
     */
    public function isSftp($destination)
    {
        if (preg_match('#^sftp:\/\/([^@]+)@([^\/]+)\/(.+)$#', $destination, $results)) {
            return $results;
        }

        return false;
    }

    /**
     * Get SFTP connection
     *
     * @param $endpoint
     * @param $username
     * @param $privateKey
     * @return SFTP
     * @throws LocalizedException
     */
    protected function _getSftpConnection($endpoint, $username, $privateKey)
    {
        // @codingStandardsIgnoreStart
        if (!isset($this->activeConnections[$endpoint . '_' . $username])) {
            $sftp = new SFTP($endpoint);
            $key = new RSA();
            $key->loadKey($privateKey);
            if (!$sftp->login($username, $key)) {
                throw new LocalizedException(__('Login Failed'));
            }

            $this->activeConnections[$endpoint . '_' . $username] = $sftp;
        }
        // @codingStandardsIgnoreEnd

        return $this->activeConnections[$endpoint . '_' . $username];
    }

    /**
     * Upload file to sftp server
     *
     * @param $connectionDetails
     * @param $privateKey
     * @param $contents
     * @throws LocalizedException
     */
    public function createFileOnRemoteServer($connectionDetails, $privateKey, $contents)
    {
        $sftp = $this->_getSftpConnection($connectionDetails[2], $connectionDetails[1], $privateKey);

        $sftp->put($connectionDetails[3], $contents);
    }

    /**
     * Download file from sftp server
     *
     * @param $connectionDetails
     * @param $privateKey
     * @param $localPath
     * @return mixed
     * @throws LocalizedException
     */
    public function retrieveFileFromRemoteServer($connectionDetails, $privateKey, $localPath)
    {
        $sftp = $this->_getSftpConnection($connectionDetails[2], $connectionDetails[1], $privateKey);

        return $sftp->get($connectionDetails[3], $localPath);
    }
}
