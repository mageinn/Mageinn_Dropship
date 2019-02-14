<?php
namespace Iredeem\Vendor\Block\Adminhtml\Batch\View\Import;

/**
 * Class Sources
 * @package Iredeem\Vendor\Block\Adminhtml\Batch\View\Import
 */
class Sources extends \Iredeem\Vendor\Block\Adminhtml\Batch\View\AbstractBatchDetails
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
                \Iredeem\Vendor\Block\Adminhtml\Batch\View\Import\Sources\Grid::class,
                'batches.destinations'
            );
        }
        return $this->_grid;
    }
}
