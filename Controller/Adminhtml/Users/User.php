<?php

namespace Mageinn\Vendor\Controller\Adminhtml\Users;

abstract class User extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageinn_Vendor::user_list';
}
