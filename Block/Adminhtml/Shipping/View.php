<?php

namespace Mageinn\Dropship\Block\Adminhtml\Shipping;

use Mageinn\Dropship\Model\Shipment;
use Mageinn\Dropship\Model\Source\ShipmentStatus;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class View
 * @package Mageinn\Dropship\Block\Adminhtml\Shipping
 */
class View extends \Magento\Shipping\Block\Adminhtml\View
{
    /**
     * @var Shipment
     */
    protected $shipment;

    /**
     * Constructor.
     * @param Context $context
     * @param Shipment $shipment
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Shipment $shipment,
        Registry $registry,
        array $data = []
    ) {
        $this->shipment = $shipment;
        return parent::__construct(
            $context,
            $registry,
            $data
        );
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->_authorization->isAllowed('Mageinn_Dropship::mark_shipped')) {
            $this->buttonList->add(
                'markShipped',
                [
                    'label'    => __('Mark as shipped'),
                    'class'    => 'primary',
                    'disabled' => $this->shipment->isStatusLocked(),
                    'onclick'  => sprintf('setLocation(\'%s\')', $this->getMarkAsShippedUrl())
                ]
            );
        }
    }

    /**
     * @return string
     */
    public function getMarkAsShippedUrl()
    {
        return $this->getUrl('sales/shipment/ship', ['shipment_id' => $this->getShipment()->getId()]);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $emailSent = $this->getShipment()->getEmailSent()
            ? __('the shipment email was sent')
            : __('the shipment email is not sent');

        // @codingStandardsIgnoreStart
        return __(
            'Shipment #%1 | %3 (%2) [%4]',
            $this->getShipment()->getIncrementId(),
            $emailSent,
            $this->formatDate(
                $this->_localeDate->date(new \DateTime($this->getShipment()->getCreatedAt())),
                \IntlDateFormatter::MEDIUM,
                true
            ),
            ShipmentStatus::getLabel($this->getShipment()->getDropshipStatus())
        );
        // @codingStandardsIgnoreEnd
    }
}
