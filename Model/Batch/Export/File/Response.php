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
namespace Mageinn\Dropship\Model\Batch\Export\File;

/**
 * Class Response
 * @package Mageinn\Dropship\Model\Batch\Export\File
 */
class Response
{
    /**
     * @var array
     */
    protected $_notes;

    /**
     * @var string
     */
    protected $_filePath;

    /**
     * @return array
     */
    public function getNotes()
    {
        return $this->_notes;
    }

    /**
     * @param $notes
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->_notes = $notes;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->_filePath;
    }

    /**
     * @param $filePath
     * @return $this
     */
    public function setFilePath($filePath)
    {
        $this->_filePath = $filePath;
        return $this;
    }
}
