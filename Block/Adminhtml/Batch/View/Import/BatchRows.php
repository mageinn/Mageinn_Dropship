<?php
namespace Iredeem\Vendor\Block\Adminhtml\Batch\View\Import;

/**
 * Class BatchRows
 * @package Iredeem\Vendor\Block\Adminhtml\Batch\View\Import
 */
class BatchRows extends \Iredeem\Vendor\Block\Adminhtml\Batch\View\AbstractBatchDetails
{

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGrid()
    {
        if (null === $this->_grid) {
            $this->_grid = $this->getLayout()->createBlock(
                \Iredeem\Vendor\Block\Adminhtml\Batch\View\Import\BatchRows\Grid::class,
                'batches.data.rows'
            );
        }
        return $this->_grid;
    }
}
