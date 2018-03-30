<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\Import;

use Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractBatchDetails;
use Mageinn\Dropship\Block\Adminhtml\Batch\View\Import\Sources\Grid;

/**
 * Class Sources
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\Import
 */
class Sources extends AbstractBatchDetails
{

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGrid()
    {
        if (is_null($this->grid)) {
            $this->grid = $this->getLayout()->createBlock(Grid::class, 'batches.destinations');
        }

        return $this->grid;
    }
}
