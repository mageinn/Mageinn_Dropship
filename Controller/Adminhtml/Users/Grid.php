<?php
namespace Mageinn\Dropship\Controller\Adminhtml\Users;

/**
 * Class Grid
 * @package Mageinn\Dropship\Controller\Adminhtml\Users
 */
class Grid extends \Mageinn\Dropship\Controller\Adminhtml\Users\User
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Mageinn\Dropship\Model\Info
     */
    protected $vendorModel;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Grid constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Mageinn\Dropship\Model\Info $vendorModel
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Mageinn\Dropship\Model\Info $vendorModel,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->registry = $registry;
        $this->vendorModel = $vendorModel;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $item = $this->_initItem();

        if (!$item) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('sales/vendor/newaction', ['_current' => true, 'id' => null]);
        }

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'Mageinn\Dropship\Block\Adminhtml\Users\Edit\Tab\User',
                'vendor.user.grid'
            )->toHtml()
        );
    }

    /**
     * @return \Mageinn\Dropship\Model\Info
     */
    protected function _initItem()
    {
        $id = (int)$this->getRequest()->getParam('id', false);
        $myModel = $this->vendorModel;

        if ($id) {
            $myModel->load($id);
        }

        $this->registry->register('mageinn_dropship', $myModel);

        return $myModel;
    }
}
