<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\Export;

/**
 * Class BatchRows
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\Export
 */
class BatchRows extends \Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractBatchDetails
{

    /**
     * Retrieve instance of grid block
     * @return \Magento\Backend\Block\Widget\Grid\Extended|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGrid()
    {
        if (null === $this->_grid) {
            $this->_grid = $this->getLayout()->createBlock(
                \Mageinn\Dropship\Block\Adminhtml\Batch\View\Export\BatchRows\Grid::class,
                'batches.data.rows'
            );
        }
        return $this->_grid;
    }
}
