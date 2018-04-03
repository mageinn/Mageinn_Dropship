<?php
namespace Mageinn\Vendor\Block\Adminhtml\Batch\View\Export\BatchRows;

/**
 * Class Grid
 * @package Mageinn\Vendor\Block\Adminhtml\Batch\View\Export\BatchRows
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
            'item_sku',
            [
                'header' => __('Item SKU'),
                'name' => 'item_sku',
                'index' => 'item_sku',
            ]
        );
        return parent::_prepareColumns();
    }
}
