<?php

namespace Mageinn\Vendor\Model\Batch\Export\File;

/**
 * Class Response
 *
 * @package Mageinn\Vendor\Model\Batch\Export
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
     * @param array $notes
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
     * @param string $filePath
     * @return Response
     */
    public function setFilePath($filePath)
    {
        $this->_filePath = $filePath;
        return $this;
    }
}
