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
 * Class BatchStatus
 * @package Mageinn\Dropship\Model\Source
 */
class BatchStatus implements OptionSourceInterface
{
    const BATCH_STATUS_PENDING    = 'pending';
    const BATCH_STATUS_SCHEDULED  = 'scheduled';
    const BATCH_STATUS_MISSED     = 'missed';
    const BATCH_STATUS_PROCESSING = 'processing';
    const BATCH_STATUS_EXPORTING  = 'exporting';
    const BATCH_STATUS_IMPORTING  = 'importing';
    const BATCH_STATUS_EMPTY      = 'empty';
    const BATCH_STATUS_SUCCESS    = 'success';
    const BATCH_STATUS_PARTIAL    = 'partial';
    const BATCH_STATUS_ERROR      = 'error';
    const BATCH_STATUS_CANCELED   = 'canceled';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BATCH_STATUS_PENDING, 'label' => __('Pending')],
            ['value' => self::BATCH_STATUS_SCHEDULED, 'label' => __('Scheduled')],
            ['value' => self::BATCH_STATUS_MISSED, 'label' => __('Missed')],
            ['value' => self::BATCH_STATUS_PROCESSING, 'label' => __('Processing')],
            ['value' => self::BATCH_STATUS_EXPORTING, 'label' => __('Exporting')],
            ['value' => self::BATCH_STATUS_IMPORTING, 'label' => __('Importing')],
            ['value' => self::BATCH_STATUS_EMPTY, 'label' => __('Empty')],
            ['value' => self::BATCH_STATUS_SUCCESS, 'label' => __('Success')],
            ['value' => self::BATCH_STATUS_PARTIAL, 'label' => __('Partial')],
            ['value' => self::BATCH_STATUS_ERROR, 'label' => __('Error')],
            ['value' => self::BATCH_STATUS_CANCELED, 'label' => __('Canceled')],
        ];
    }

    /**
     * @return array
     */
    public function getOptionsArray()
    {
        return [
            self::BATCH_STATUS_PENDING => __('Pending'),
            self::BATCH_STATUS_SCHEDULED => __('Scheduled'),
            self::BATCH_STATUS_MISSED => __('Missed'),
            self::BATCH_STATUS_PROCESSING => __('Processing'),
            self::BATCH_STATUS_EXPORTING => __('Exporting'),
            self::BATCH_STATUS_IMPORTING => __('Importing'),
            self::BATCH_STATUS_EMPTY => __('Empty'),
            self::BATCH_STATUS_SUCCESS => __('Success'),
            self::BATCH_STATUS_PARTIAL => __('Partial'),
            self::BATCH_STATUS_ERROR => __('Error'),
            self::BATCH_STATUS_CANCELED => __('Canceled'),
        ];
    }
}
