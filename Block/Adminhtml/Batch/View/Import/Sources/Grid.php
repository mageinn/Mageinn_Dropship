<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\Import\Sources;

/**
 * Class Grid
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\Import\Sources
 */
class Grid extends \Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractFilePath\Grid
{
    /**
     * @return \Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractFilePath\Grid
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'name' => 'entity_id',
                'index' => 'entity_id',
            ]
        )->addColumn(
            'file_path',
            [
                'header' => __('Location'),
                'name' => 'file_path',
                'index' => 'file_path',
            ]
        )->addColumn(
            'status',
            [
                'header' => __('Status'),
                'name' => 'status',
                'index' => 'status',
                'type' => 'options',
                'options' => $this->_batchStatus->getOptionsArray(),
            ]
        )->addColumn(
            'error_info',
            [
                'header' => __('Error'),
                'name' => 'error_info',
                'index' => 'error_info',
            ]
        )->addColumn(
            'updated_at',
            [
                'header' => __('Updated At'),
                'name' => 'updated_at',
                'index' => 'updated_at',
            ]
        );

        return parent::_prepareColumns();
    }
}
