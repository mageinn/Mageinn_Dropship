<?php
namespace Mageinn\Vendor\Block\Adminhtml\Batch\View\Import\BatchRows;

/**
 * Class Grid
 * @package Mageinn\Vendor\Block\Adminhtml\Batch\View\Import\BatchRows
 */
class Grid extends \Mageinn\Vendor\Block\Adminhtml\Batch\View\AbstractBatchRows\Grid
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
                'header' => __('ID'),
                'name' => 'entity_id',
                'index' => 'entity_id',
            ]
        )->addColumn(
            'order_increment_id',
            [
                'header' => __('Order ID'),
                'name' => 'order_increment_id',
                'index' => 'order_increment_id',
            ]
        )->addColumn(
            'shipment_increment_id',
            [
                'header' => __('Shipment ID'),
                'name' => 'shipment_increment_id',
                'index' => 'shipment_increment_id',
            ]
        )->addColumn(
            'track_id',
            [
                'header' => __('Tracking ID'),
                'name' => 'track_id',
                'index' => 'track_id',
            ]
        )->addColumn(
            'has_error',
            [
                'header' => __('Has Error'),
                'name' => 'has_error',
                'index' => 'has_error',
            ]
        )->addColumn(
            'error_info',
            [
                'header' => __('Error Info'),
                'name' => 'error_info',
                'index' => 'error_info',
            ]
        );
        return parent::_prepareColumns();
    }
}
