<?php
namespace Mageinn\Vendor\Model\Source;

use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class BatchType
 * @package Mageinn\Vendor\Model\Source
 */
class BatchType implements OptionSourceInterface
{
    const MAGEINN_VENDOR_BATCH_TYPE_EXPORT = 'orders_export';
    const MAGEINN_VENDOR_BATCH_TYPE_IMPORT = 'orders_import';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::MAGEINN_VENDOR_BATCH_TYPE_IMPORT, 'label' => __('Import')],
            ['value' => self::MAGEINN_VENDOR_BATCH_TYPE_EXPORT, 'label' => __('Export')],
        ];
    }
}
