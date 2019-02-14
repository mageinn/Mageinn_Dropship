<?php

namespace Iredeem\Vendor\Model\Batch\Export;

/**
 * Class Response
 *
 * @package Iredeem\Vendor\Model\Batch\Export
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
     * Setter for the rows number property
     *
     * @param $number
     * @return $this
     */
    public function setRowsNumber($number)
    {
        $this->rowsNumber = $number;

        return $this;
    }

    /**
     * Getter for the rows number property
     *
     * @return int
     */
    public function getRowsNumber()
    {
        return $this->rowsNumber;
    }

    /**
     * Setter for the rows text property
     *
     * @param $text
     * @return $this
     */
    public function setRowsText($text)
    {
        $this->rowsText = $text;

        return $this;
    }

    /**
     * Getter for the rows text property
     *
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
     * @param array $notes
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
     * @param string $filePath
     * @return Response
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
     * @param array $batchRows
     * @return Response
     */
    public function setBatchRows($batchRows)
    {
        $this->batchRows = $batchRows;
        return $this;
    }
}
