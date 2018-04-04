<?php
namespace Mageinn\Vendor\Block\Adminhtml\Batch\View\Import;

/**
 * Class BatchRows
 * @package Mageinn\Vendor\Block\Adminhtml\Batch\View\Import
 */
class BatchRows extends \Mageinn\Vendor\Block\Adminhtml\Batch\View\AbstractBatchDetails
{

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGrid()
    {
        if (is_null($this->_grid)) {
            $this->_grid = $this->getLayout()->createBlock(
                \Mageinn\Vendor\Block\Adminhtml\Batch\View\Import\BatchRows\Grid::class,
                'batches.data.rows'
            );
        }
        return $this->_grid;
    }
}
