<?php
namespace Iredeem\Vendor\Block\Adminhtml\Batch\View\Export\Destinations;

/**
 * Class Grid
 * @package Iredeem\Vendor\Block\Adminhtml\Batch\View\Export\Destinations
 */
class Grid extends \Iredeem\Vendor\Block\Adminhtml\Batch\View\AbstractFilePath\Grid
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

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        $row->getData();
        return $this->getUrl('sales/batches/download', ['_current' => true]);
    }
}
