<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\Import\BatchRows;

use Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractBatchRows\Grid as GridParent;

/**
 * Class Grid
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\Import\BatchRows
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
            'order_increment_id',
            [
                'name' => 'order_increment_id',
                'index' => 'order_increment_id',
                'header' => __('Order ID'),
            ]
        );

        $this->addColumn(
            'shipment_increment_id',
            [
                'name' => 'shipment_increment_id',
                'index' => 'shipment_increment_id',
                'header' => __('Shipment ID'),
            ]
        );

        $this->addColumn(
            'track_id',
            [
                'name' => 'track_id',
                'index' => 'track_id',
                'header' => __('Tracking ID'),
            ]
        );

        $this->addColumn(
            'has_error',
            [
                'name' => 'has_error',
                'index' => 'has_error',
                'header' => __('Has Error'),
            ]
        );

        $this->addColumn(
            'error_info',
            [
                'name' => 'error_info',
                'index' => 'error_info',
                'header' => __('Error Info'),
            ]
        );

        return parent::_prepareColumns();
    }
}
