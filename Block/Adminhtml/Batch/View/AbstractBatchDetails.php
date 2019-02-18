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
    protected $_template = 'Mageinn_Dropship::batch/view/details.phtml';

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected $_grid;

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    public function getGrid()
    {
        return $this->_grid;
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getGrid()->toHtml();
    }
}
