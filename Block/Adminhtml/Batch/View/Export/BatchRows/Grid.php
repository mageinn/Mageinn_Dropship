<?php
/**
 * Mageinn
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageinn.com license that is
 * available through the world-wide-web at this URL:
 * https://mageinn.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */
namespace Mageinn\Dropship\Block\Adminhtml\Batch\View\Export\BatchRows;

/**
 * Class Grid
 * @package Mageinn\Dropship\Block\Adminhtml\Batch\View\Export\BatchRows
 */
class Grid extends \Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractBatchRows\Grid
{
    /**
     * @return \Mageinn\Dropship\Block\Adminhtml\Batch\View\AbstractBatchRows\Grid
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
