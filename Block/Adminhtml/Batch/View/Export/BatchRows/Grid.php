<?php
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\Export\BatchRows;

/**
 * Class Grid
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\Export\BatchRows
 */
class Grid extends \Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractBatchRows\Grid
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
            'order_increment_id',
            [
                'name' => 'order_increment_id',
                'index' => 'order_increment_id',
                'header' => __('Order ID'),
            ]
        )->addColumn(
            'shipment_increment_id',
            [
                'name' => 'shipment_increment_id',
                'index' => 'shipment_increment_id',
                'header' => __('Shipment ID'),
            ]
        )->addColumn(
            'item_sku',
            [
                'name' => 'item_sku',
                'index' => 'item_sku',
                'header' => __('Item SKU'),
            ]
        );
        return parent::_prepareColumns();
    }
}
