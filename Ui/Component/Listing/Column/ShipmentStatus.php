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
namespace Mageinn\Dropship\Ui\Component\Listing\Column;

use Mageinn\Dropship\Model\Source\ShipmentStatus as ShipmentStatusSource;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ShipmentStatus
 * @package Mageinn\Dropship\Ui\Component\Listing\Column
 */
class ShipmentStatus extends Column
{
    /**
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
