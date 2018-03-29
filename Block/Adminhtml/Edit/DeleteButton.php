<?php

namespace Mageinn\Dropship\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

/**
 * Class SaveButton
 * @package Magento\Customer\Block\Adminhtml\Edit
 */
class DeleteButton implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Return CMS block ID
     *
     * @return int|null
     */
    public function getVendorId()
    {
        return $this->context->getRequest()->getParam('id');
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->context->getUrlBuilder()->getUrl('*/*/delete', ['entity_id' => $this->getVendorId()]);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getVendorId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm
                (\'' . __('Are you sure you want to do this?') . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }
}
