<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\Export\Destinations;

/**
 * Class Grid
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\Export\Destinations
 */
class Grid extends \Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractFilePath\Grid
{
    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'name' => 'entity_id',
                'index' => 'entity_id',
                'header' => __('ID'),
            ]
        )->addColumn(
            'file_path',
            [
                'name' => 'file_path',
                'index' => 'file_path',
                'header' => __('Location'),
            ]
        )->addColumn(
            'status',
            [
                'name' => 'status',
                'index' => 'status',
                'header' => __('Status'),
                'type' => 'options',
                'options' => $this->batchStatus->getOptionsArray(),
            ]
        )->addColumn(
            'error_info',
            [
                'name' => 'error_info',
                'index' => 'error_info',
                'header' => __('Error'),
            ]
        )->addColumn(
            'updated_at',
            [
                'name' => 'updated_at',
                'index' => 'updated_at',
                'header' => __('Updated At'),
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        $row->getData(); // TODO why?
        return $this->getUrl('sales/batches/download', ['_current' => true]);
    }
}
