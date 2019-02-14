<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View;

/**
 * Class AbstractBatchDetails
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View
 */
abstract class AbstractBatchDetails extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Iredeem_Vendor::batch/view/details.phtml';

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected $_grid;

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    public function getGrid()
    {
        return $this->_grid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getGrid()->toHtml();
    }
}
