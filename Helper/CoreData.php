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

namespace Mageinn\Dropship\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\App\Helper\Context;
use \Magento\Store\Model\ScopeInterface;

/**
 * Class CoreData
 * @package Mageinn\Dropship\Helper
 */
class CoreData extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * CoreData constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager  = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param $data
     * @throws LocalizedException
     */
    public function bulkInsert(\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource, $data)
    {
        $connection = $resource->getConnection();
        try {
            $connection->beginTransaction();
            $connection->insertMultiple($resource->getMainTable(), $data);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param $data
     * @param array $fields
     * @throws LocalizedException
     */
    public function bulkUpdate(\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource, $data, $fields = [])
    {
        $connection = $resource->getConnection();
        try {
            $connection->beginTransaction();
            $connection->insertOnDuplicate($resource->getMainTable(), $data, $fields);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param $field
     * @param $values
     * @throws LocalizedException
     */
    public function bulkDelete(\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource, $field, $values)
    {
        $connection = $resource->getConnection();
        try {
            $condition = $connection->quoteInto($field . ' IN (?)', $values);
            $connection->beginTransaction();
            $connection->delete($resource->getMainTable(), $condition);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param $field
     * @param $values
     * @param $data
     * @throws LocalizedException
     */
    public function bulkDeleteAndInsert(
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource,
        $field,
        $values,
        $data
    ) {
        $connection = $resource->getConnection();
        try {
            $condition = $connection->quoteInto($field . ' IN (?)', $values);
            $connection->beginTransaction();
            $connection->delete($resource->getMainTable(), $condition);
            $connection->insertMultiple($resource->getMainTable(), $data);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreToBaseCurrencyRate()
    {
        $rate = 1;
        $store = $this->storeManager->getStore();
        $currency = $store->getCurrentCurrencyCode();
        if ($currency != $store->getBaseCurrencyCode()) {
            $rate = $store->getBaseCurrency()->getRate($currency);
        }

        return $rate;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    }
}