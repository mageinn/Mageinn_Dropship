<?php
namespace Mageinn\Vendor\Controller\Adminhtml\Vendor;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\ForwardFactory;

/**
 * Vendor Index Action
 * @package Mageinn\Vendor\Controller\Adminhtml\Vendor
 */
class NewAction extends Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(Context $context, ForwardFactory $resultForwardFactory)
    {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Create new customer action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('edit');
        return $resultForward;
    }
}
