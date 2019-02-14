<?php
namespace Iredeem\Vendor\Block\Adminhtml\Batch\View\Export;

/**
 * Class DataRows
 * @package Iredeem\Vendor\Block\Adminhtml\Batch\View\Export
 */
class Destinations extends \Iredeem\Vendor\Block\Adminhtml\Batch\View\AbstractBatchDetails
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
                \Iredeem\Vendor\Block\Adminhtml\Batch\View\Export\Destinations\Grid::class,
                'batches.destinations'
            );
        }
        return $this->_grid;
    }
}
