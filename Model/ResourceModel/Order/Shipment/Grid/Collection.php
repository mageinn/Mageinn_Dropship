<?php

namespace Iredeem\Vendor\Model\ResourceModel\Order\Shipment\Grid;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Sales\Model\ResourceModel\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Grid\Collection as GridCollection;
use Psr\Log\LoggerInterface as Logger;

class Collection extends GridCollection
{
    /**
     * @var Session
     */
    protected $authSession;

    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param Session $authSession
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        Session $authSession,
        $mainTable = 'sales_shipment_grid',
        $resourceModel = Shipment::class
    ) {
        $this->authSession = $authSession;
        $this->_mainTable = $mainTable;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $user = $this->authSession->getUser();

        if ($user && $user->getAssocVendorId()) {
            $this->addFieldToFilter('vendor_id', [
                'in' => json_decode($user->getAssocVendorId())
            ]);
        }

        return $this;
    }
}
