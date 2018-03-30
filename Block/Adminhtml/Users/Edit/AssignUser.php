<?php

namespace Mageinn\Dropship\Block\Adminhtml\Users\Edit;
use Mageinn\Dropship\Block\Adminhtml\Users\Edit\Tab\User;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\User\Model\ResourceModel\User\CollectionFactory;

/**
 * Class AssignUser
 * @package Mageinn\Dropship\Block\Adminhtml\Users\Edit
 */
class AssignUser extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Mageinn_Dropship::vendor/assign_user.phtml';

    /**
     * @var User
     *
     */
    protected $blockGrid;

    /**
     * @var Registry
     *
     */
    protected $registry;

    /**
     * @var EncoderInterface
     *
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection
     *
     */
    protected $userCollectionFactory;

    /**
     * Constructor.
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param CollectionFactory $userCollectionFactory
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        CollectionFactory $userCollectionFactory,
        Registry $registry,
        array $data = []
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->registry = $registry;
        $this->userCollectionFactory = $userCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     * @return Tab\User|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (is_null($this->blockGrid)) {
            $this->blockGrid = $this->getLayout()->createBlock(User::class, 'vendor.user.grid');
        }

        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getUsersJson()
    {
        if ($this->getVendor()->getId()) {
            $v_users = $this->userCollectionFactory->create()->addFieldToFilter('assoc_vendor_id', [
                    'like' => '%"' . $this->getVendor()->getId() . '"%'
                ])->addFieldToSelect('user_id');
            $users = [];
            foreach ($v_users as $user) {
                $users[$user->getUserId()]  = $user->getUserId();
            }

            if (!empty($users)) return $this->jsonEncoder->encode($users);
        }

        return '{}';
    }

    /**
     * Retrieve current category instance
     * @return array|null
     */
    public function getVendor()
    {
        return $this->registry->registry('iredeem_vendor');
    }
}
