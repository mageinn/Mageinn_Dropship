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
namespace Mageinn\Dropship\Model;

use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Shipment
 * @package Mageinn\Dropship\Model
 */
class Shipment extends \Magento\Sales\Model\Order\Shipment
{
    const DROPSHIP_STATUS = 'dropship_status';

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * Shipment constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Item\CollectionFactory $shipmentItemCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param \Magento\Sales\Model\Order\Shipment\CommentFactory $commentFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Comment\CollectionFactory $commentCollectionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Item\CollectionFactory $shipmentItemCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
        \Magento\Sales\Model\Order\Shipment\CommentFactory $commentFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Comment\CollectionFactory $commentCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->authSession = $authSession;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $shipmentItemCollectionFactory,
            $trackCollectionFactory,
            $commentFactory,
            $commentCollectionFactory,
            $orderRepository,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Comment|string $comment
     * @param bool $notify
     * @param bool $visibleOnFront
     * @return $this|\Magento\Sales\Model\Order\Shipment
     * @throws \Exception
     */
    public function addComment($comment, $notify = false, $visibleOnFront = false)
    {
        if (!$comment instanceof \Magento\Sales\Model\Order\Shipment\Comment) {
            $comment = $this->_commentFactory->create()->setComment(
                $comment
            )->setIsCustomerNotified(
                $notify
            )->setIsVisibleOnFront(
                $visibleOnFront
            )->setStatus($this->getDropshipStatus());
        }

        $comment->setShipment($this)->setParentId($this->getId())->setStoreId($this->getStoreId());

        if (!$comment->getId()) {
            $this->getCommentsCollection()->addItem($comment);
        }

        $this->_hasDataChanges = true;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDropshipStatus()
    {
        return $this->getData(self::DROPSHIP_STATUS);
    }

    public function setDropshipStatus($status)
    {
        return $this->setData(self::DROPSHIP_STATUS, $status);
    }

    /**
     * @return bool
     */
    public function isStatusLocked()
    {
        $admin = $this->authSession->getUser();
        $shipment = $this->_registry->registry('current_shipment');
        if (!empty($admin->getAssocVendorId()) &&
            (int) $shipment->getDropshipStatus()
            === \Mageinn\Dropship\Model\Source\ShipmentStatus::SHIPMENT_STATUS_SHIPPED
        ) {
            return true;
        }

        return false;
    }
}
