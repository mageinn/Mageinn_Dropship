<?php
namespace Mageinn\Vendor\Ui\Component\Listing\Column;

use Mageinn\Vendor\Model\Source\ShipmentStatus as ShipmentStatusSource;
use Magento\Ui\Component\Listing\Columns\Column;

class ShipmentStatus extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['dropship_status'] = ShipmentStatusSource::getLabel($item['dropship_status']);
            }
        }
        return $dataSource;
    }
}
