<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\Import\Sources;

use Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractFilePath\Grid as GridParent;

/**
 * Class Grid
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\Import\Sources
 */
class Grid extends GridParent
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
        );

        $this->addColumn(
            'file_path',
            [
                'name' => 'file_path',
                'index' => 'file_path',
                'header' => __('Location'),
            ]
        );

        $this->addColumn(
            'status',
            [
                'name' => 'status',
                'index' => 'status',
                'header' => __('Status'),
                'type' => 'options',
                'options' => $this->batchStatus->getOptionsArray(),
            ]
        );

        $this->addColumn(
            'error_info',
            [
                'name' => 'error_info',
                'index' => 'error_info',
                'header' => __('Error'),
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'name' => 'updated_at',
                'index' => 'updated_at',
                'header' => __('Updated At'),
            ]
        );

        return parent::_prepareColumns();
    }
}
