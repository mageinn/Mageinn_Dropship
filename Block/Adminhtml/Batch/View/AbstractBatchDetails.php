<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View;

use Magento\Backend\Block\Template;

/**
 * Class AbstractBatchDetails
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View
 */
abstract class AbstractBatchDetails extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Mageinn_Dropship::batch/view/details.phtml';

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected $grid;

    /**
     * Retrieve instance of grid block
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * Return HTML of grid block
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getGrid()->toHtml();
    }
}
