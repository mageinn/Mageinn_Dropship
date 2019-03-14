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
namespace Mageinn\Dropship\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Address
 * @package Mageinn\Dropship\Model\ResourceModel
 */
class Address extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageinn_dropship_address', 'entity_id');
    }
}
