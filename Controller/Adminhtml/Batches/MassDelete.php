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
namespace Mageinn\Dropship\Controller\Adminhtml\Batches;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Mageinn\Dropship\Model\ResourceModel\Batch\CollectionFactory;

/**
 * Class MassDelete
 * @package Mageinn\Dropship\Controller\Adminhtml\Batches
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var string
     */
    protected $redirectUrl = '*/*/';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            /** @var \Mageinn\Dropship\Model\Batch $item */
            foreach ($collection as $item) {
                $item->delete();
            }

            $this->messageManager->addSuccess(__('Batches deleted successfully.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath($this->redirectUrl);

            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath($this->redirectUrl);
        }
    }
}
