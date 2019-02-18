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
namespace Mageinn\Dropship\Block\Adminhtml\Shipping;

use Mageinn\Dropship\Model\Source\ShipmentStatus;

/**
 * Class View
 * @package Mageinn\Dropship\Block\Adminhtml\Shipping
 */
class View extends \Magento\Shipping\Block\Adminhtml\View
{
    /**
     * @var \Mageinn\Dropship\Model\Shipment
     */
    protected $shipment;

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Mageinn\Dropship\Model\Shipment $shipment
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Mageinn\Dropship\Model\Shipment $shipment,
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

        if ($this->_authorization->isAllowed('Mageinn_Dropship::mark_shipped')) {
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

    /**
     * @return string
     */
    public function getMarkAsShippedUrl()
    {
        return $this->getUrl('sales/shipment/ship', ['shipment_id' => $this->getShipment()->getId()]);
    }

    /**
     * @return \Magento\Framework\Phrase
     * @throws \Exception
     */
    public function getHeaderText()
    {
        if ($this->getShipment()->getEmailSent()) {
            $emailSent = __('the shipment email was sent');
        } else {
            $emailSent = __('the shipment email is not sent');
        }
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
    }
}
