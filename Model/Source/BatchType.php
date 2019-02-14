<?php
namespace Mageinn\Dropship\Model\Source;

use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class BatchType
 * @package Mageinn\Dropship\Model\Source
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
