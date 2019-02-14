<?php

namespace Iredeem\Vendor\Model;

use Magento\Framework\Api\AttributeValueFactory;

/**
 * Sales order shipment model
 *
 * @api
 * @method \Magento\Sales\Model\Order\Invoice setSendEmail(bool $value)
 * @method \Magento\Sales\Model\Order\Invoice setCustomerNote(string $value)
 * @method string getCustomerNote()
 * @method \Magento\Sales\Model\Order\Invoice setCustomerNoteNotify(bool $value)
 * @method bool getCustomerNoteNotify()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @since 100.0.2
 */
class Shipment extends \Magento\Sales\Model\Order\Shipment
{
    const DROPSHIP_STATUS = 'dropship_status';

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

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
     * Adds comment to shipment with additional possibility to send it to customer via email
     * and show it in customer account
     *
     * @param \Magento\Sales\Model\Order\Shipment\Comment|string $comment
     * @param bool $notify
     * @param bool $visibleOnFront
     * @return $this
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
     * Returns dropship_status
     *
     * @return string
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
     * Check if vendor can change shipment status
     *
     * @return bool
     */
    public function isStatusLocked()
    {
        $admin = $this->authSession->getUser();
        $shipment = $this->_registry->registry('current_shipment');
        if (!empty($admin->getAssocVendorId()) &&
            (int) $shipment->getDropshipStatus()
            === \Iredeem\Vendor\Model\Source\ShipmentStatus::SHIPMENT_STATUS_SHIPPED
        ) {
            return true;
        }

        return false;
    }
}
