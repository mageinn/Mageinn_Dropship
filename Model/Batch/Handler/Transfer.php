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
namespace Mageinn\Dropship\Model\Batch\Handler;

use Magento\Framework\Exception\LocalizedException;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;

/**
 * Class Transfer
 * @package Mageinn\Dropship\Model\Batch\Handler
 */
class Transfer
{
    protected $activeConnections = [];

    /**
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
     * @param $endpoint
     * @param $username
     * @param $privateKey
     * @return mixed
     * @throws LocalizedException
     */
    protected function _getSftpConnection($endpoint, $username, $privateKey)
    {
        if (!isset($this->activeConnections[$endpoint . '_' . $username])) {
            $sftp = new SFTP($endpoint);
            $key = new RSA();
            $key->loadKey($privateKey);
            if (!$sftp->login($username, $key)) {
                throw new LocalizedException(__('Login Failed'));
            }

            $this->activeConnections[$endpoint . '_' . $username] = $sftp;
        }

        return $this->activeConnections[$endpoint . '_' . $username];
    }

    /**
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
