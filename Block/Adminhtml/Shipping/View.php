<?php

namespace Mageinn\Vendor\Block\Adminhtml\Shipping;

use Mageinn\Vendor\Model\Source\ShipmentStatus;

/**
 * Class View
 * @package Mageinn\Vendor\Block\Adminhtml\Shipping
 */
class View extends \Magento\Shipping\Block\Adminhtml\View
{
    /**
     * @var \Mageinn\Vendor\Model\Shipment
     */
    protected $shipment;

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mageinn\Vendor\Model\Shipment $shipment
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Mageinn\Vendor\Model\Shipment $shipment,
        array $data = []
    ) {
        $this->shipment = $shipment;
        return parent::__construct($context, $registry, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->_authorization->isAllowed('Mageinn_Vendor::mark_shipped')) {
            $this->buttonList->add(
                'markShipped',
                [
                    'label'    => __('Mark as shipped'),
                    'class'    => 'primary',
                    'disabled' => $this->shipment->isStatusLocked(),
                    'onclick'  => 'setLocation(\'' . $this->getMarkAsShippedUrl() . '\')'
                ]
            );
        }
    }

    public function getMarkAsShippedUrl()
    {
        return $this->getUrl('sales/shipment/ship', ['shipment_id' => $this->getShipment()->getId()]);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->getShipment()->getEmailSent()) {
            $emailSent = __('the shipment email was sent');
        } else {
            $emailSent = __('the shipment email is not sent');
        }
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
