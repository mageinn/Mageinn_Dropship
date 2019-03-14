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
namespace Mageinn\Dropship\Model\Source;

use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class BatchType
 * @package Mageinn\Dropship\Model\Source
 */
class BatchType implements OptionSourceInterface
{
    const MAGEINN_DROPSHIP_BATCH_TYPE_EXPORT = 'orders_export';
    const MAGEINN_DROPSHIP_BATCH_TYPE_IMPORT = 'orders_import';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::MAGEINN_DROPSHIP_BATCH_TYPE_IMPORT, 'label' => __('Import')],
            ['value' => self::MAGEINN_DROPSHIP_BATCH_TYPE_EXPORT, 'label' => __('Export')],
        ];
    }
}
