<?php
namespace Mageinn\Dropship\Controller\Adminhtml\ShippingRates;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

/**
 * Dropship Index Action
 * @package Mageinn\Dropship\Controller\Adminhtml\Dropship
 */
class Filter extends Action
{
    /**
     * @var \Mageinn\Dropship\Model\ShippingRate\Attribute\Source\ShippingRuleset
     */
    private $ratesSource;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mageinn\Dropship\Model\ShippingRate\Attribute\Source\ShippingRuleset $ratesSource
     */
    public function __construct(
        Context $context,
        \Mageinn\Dropship\Model\ShippingRate\Attribute\Source\ShippingRuleset $ratesSource
    ) {
        parent::__construct($context);

        $this->ratesSource = $ratesSource;
    }

    /**
     * @return string
     */
    public function execute()
    {
        $vendorId = $this->getRequest()->getParam('vendor');
        $options = $this->ratesSource->getVendorOptions($vendorId);

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($options);

        return $resultJson;
    }
}
