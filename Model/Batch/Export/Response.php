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
namespace Mageinn\Dropship\Model\Batch\Export;

/**
 * Class Response
 * @package Mageinn\Dropship\Model\Batch\Export
 */
class Response
{
    /**
     * @var int
     */
    protected $rowsNumber;

    /**
     * @var string
     */
    protected $rowsText;

    /**
     * @var string
     */
    protected $notes;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var array;
     */
    protected $batchRows;

    /**
     * @param $number
     * @return $this
     */
    public function setRowsNumber($number)
    {
        $this->rowsNumber = $number;

        return $this;
    }

    /**
     * @return int
     */
    public function getRowsNumber()
    {
        return $this->rowsNumber;
    }

    /**
     * @param $text
     * @return $this
     */
    public function setRowsText($text)
    {
        $this->rowsText = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getRowsText()
    {
        return $this->rowsText;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param $notes
     * @return $this
     */
    public function setNotes($notes)
    {
        $notesString = implode(PHP_EOL, $notes);
        $this->notes = $notesString;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param $filePath
     * @return $this
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @return array
     */
    public function getBatchRows()
    {
        return $this->batchRows;
    }

    /**
     * @param $batchRows
     * @return $this
     */
    public function setBatchRows($batchRows)
    {
        $this->batchRows = $batchRows;
        return $this;
    }
}
