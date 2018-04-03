<?php
namespace Mageinn\Vendor\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class Region
 * @package Mageinn\Vendor\Model
 */
class Region extends AbstractModel
{
    /**#@+
     * Table
     */
    const REGIONS_TABLE = 'mageinn_regions';
    /**#@-*/

    /**#@+
     * REGION Information Data
     */
    const REGION_DATA_COUNTRY = 'country';

    const REGION_DATA_NAME = 'name';

    const REGION_DATA_CODE = 'code';
    /**#@-*/

    /**
     * Object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageinn\Vendor\Model\ResourceModel\Region::class);
    }
}
