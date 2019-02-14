<?php
namespace Iredeem\Vendor\Model\Source;

use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class BatchType
 * @package Iredeem\Vendor\Model\Source
 */
class BatchType implements OptionSourceInterface
{
    const IREDEEM_VENDOR_BATCH_TYPE_EXPORT = 'orders_export';
    const IREDEEM_VENDOR_BATCH_TYPE_IMPORT = 'orders_import';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::IREDEEM_VENDOR_BATCH_TYPE_IMPORT, 'label' => __('Import')],
            ['value' => self::IREDEEM_VENDOR_BATCH_TYPE_EXPORT, 'label' => __('Export')],
        ];
    }
}
