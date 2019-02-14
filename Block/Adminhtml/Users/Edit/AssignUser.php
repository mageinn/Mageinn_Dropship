<?php

namespace Iredeem\Vendor\Block\Adminhtml\Users\Edit;

/**
 * Class Stock
 * @package Iredeem\Vendor\Block\Adminhtml\Edit\
 */
class AssignUser extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Iredeem_Vendor::vendor/assign_user.phtml';

    /**
     * @var \Iredeem\Vendor\Block\Adminhtml\Users\Edit\Tab\User
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    protected $userCollectionFactory;

    /**
     * AssignUser constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->userCollectionFactory = $userCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Iredeem\Vendor\Block\Adminhtml\Users\Edit\Tab\User::class,
                'vendor.user.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
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
            $vUsers = $this->userCollectionFactory->create()
                ->addFieldToFilter('assoc_vendor_id', [
                    'like' => '%"' . $this->getVendor()->getId() . '"%'
                ])
                ->addFieldToSelect('user_id');
            $users = [];
            foreach ($vUsers as $user) {
                $users[$user->getUserId()]  = $user->getUserId();
            }

            if (!empty($users)) {
                return $this->jsonEncoder->encode($users);
            }
        }

        return '{}';
    }

    /**
     * Retrieve current category instance
     *
     * @return array|null
     */
    public function getVendor()
    {
        return $this->registry->registry('iredeem_vendor');
    }
}
