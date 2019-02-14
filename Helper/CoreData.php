<?php
/**
 * Created by PhpStorm.
 * User: d1sho
 * Date: 13.02.19
 * Time: 9:40
 */

namespace Mageinn\Dropship\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\App\Helper\Context;
use \Magento\Store\Model\ScopeInterface;

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
     * Data constructor.
     *
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
     * General method for getting a config value.
     *
     * Will be called for the specific configs in the future modules
     *
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
     * Insert multiple values
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param $data
     * @throws LocalizedException
     * @codeCoverageIgnore - SQl function
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
     * Update multiple values
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     * @param array $data
     * @param array $fields
     * @throws LocalizedException
     * @codeCoverageIgnore - SQl function
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
     * @codeCoverageIgnore - Covered by Integration
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
     * @param $data
     * @throws LocalizedException
     * @codeCoverageIgnore - Covered by Integration
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
     * Return the rate of the current store to the base currency
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @codeCoverageIgnore - Covered by Acceptance
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
     * Returns the Currency symbol for the current currency
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @codeCoverageIgnore - Covered by Acceptance
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    }
}