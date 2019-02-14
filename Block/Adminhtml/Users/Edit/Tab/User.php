<?php

namespace Mageinn\Dropship\Block\Adminhtml\Users\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use \Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\User\Model\ResourceModel\User\CollectionFactory;
use Magento\User\Model\UserFactory;

/**
 * Class Stock
 * @package Mageinn\Dropship\Block\Adminhtml\Edit\Tab
 */
class User extends Extended
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var UserFactory
     */
    protected $userFactory;

    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    protected $userCollectionFactory;

    /**
     * User constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        UserFactory $userFactory,
        CollectionFactory $userCollectionFactory,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->userFactory = $userFactory;
        $this->coreRegistry = $coreRegistry;
        $this->userCollectionFactory = $userCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Costructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('mageinn_dropship_users');
        $this->setDefaultSort('user_id');
        $this->setUseAjax(true);
    }

    /**
     * @return array|null
     */
    public function getVendor()
    {
        return $this->coreRegistry->registry('mageinn_dropship');
    }

    /**
     * @param Column $column
     * @return $this
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in associated user flag
        if ($column->getId() == 'associated_user') {
            $usersIds = $this->_getSelectedUsers();
            if (empty($usersIds)) {
                $usersIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('user_id', ['in' => $usersIds]);
            } elseif (!empty($usersIds)) {
                $this->getCollection()->addFieldToFilter('user_id', ['nin' => $usersIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        if ($this->getVendor()->getId()) {
            $this->setDefaultFilter(['associated_user' => 1]);
        }

        $collection = $this->userCollectionFactory->create()->addFieldToSelect(
            'user_id'
        )->addFieldToSelect(
            'username'
        )->addFieldToSelect(
            'firstname'
        )->addFieldToSelect(
            'lastname'
        )->addFieldToSelect(
            'email'
        )->addFieldToSelect(
            'is_active'
        )->addFieldToSelect(
            'assoc_vendor_id'
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'associated_user',
            [
                'type' => 'checkbox',
                'name' => 'associated_user',
                'values' => $this->_getSelectedUsers(),
                'index' => 'user_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'user_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'user_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('username', ['header' => __('User Name'), 'index' => 'username']);
        $this->addColumn('firstname', ['header' => __('First Name'), 'index' => 'firstname']);
        $this->addColumn('lastname', ['header' => __('Last Name'), 'index' => 'lastname']);
        $this->addColumn('email', ['header' => __('Email'), 'index' => 'email']);
        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'type' => 'options',
                'options' => ['1' => __('Active'), '0' => __('Inactive')]
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sales/users/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedUsers()
    {
        $users = $this->getRequest()->getPost('selected_users');

        if ($users === null && $this->getVendor()->getId()) {
            $vUsers = $this->userCollectionFactory->create()
                ->addFieldToFilter('assoc_vendor_id', [
                    'like' => '%"' . $this->getVendor()->getId() . '"%'
                ])
                ->addFieldToSelect('user_id');
            $users = [];
            foreach ($vUsers as $user) {
                $users[$user->getId()] = $user->getId();
            }
        }

        return $users;
    }
}
