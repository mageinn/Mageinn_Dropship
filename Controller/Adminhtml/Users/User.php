<?php

namespace Mageinn\Dropship\Controller\Adminhtml\Users;

abstract class User extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Iredeem_Vendor::user_list';
}
