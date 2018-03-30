<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\Export;

use Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractBatchDetails;
use Mageinn\Dropship\Block\Adminhtml\Batch\View\Export\BatchRows\Grid;

/**
 * Class BatchRows
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\Export
 */
class BatchRows extends AbstractBatchDetails
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
            $this->grid = $this->getLayout()->createBlock(Grid::class, 'batches.data.rows');
        }


        return $this->grid;
    }
}
