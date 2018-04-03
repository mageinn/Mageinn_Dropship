<?php
namespace Mageinn\Vendor\Controller\Adminhtml\Vendor;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Exception\LocalizedException;
use \Mageinn\Vendor\Model\Info;
use \Mageinn\Vendor\Model\Address;
use \Mageinn\Vendor\Model\ResourceModel\Address\CollectionFactory as AddressCollectionFactory;
use \Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use \Mageinn\Vendor\Helper\Data;

/**
 * Class Save
 *
 * @package Mageinn\Vendor\Controller\Adminhtml\Vendor
 */
class Save extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var AddressCollectionFactory
     */
    protected $addressCollection;

    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    protected $userCollectionFactory;

    /**
     * @var Address
     */
    protected $addressModel;

    /**
     * @var Info
     */
    protected $vendorModel;

    /**
     * @var \Mageinn\Vendor\Helper\Data
     */
    protected $dataHelper;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param AddressCollectionFactory $addressCollectionFactory
     * @param UserCollectionFactory $userCollectionFactory
     * @param Address $addressModel
     * @param Info $vendorModel
     * @param CoreData $coreHelper
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        AddressCollectionFactory $addressCollectionFactory,
        UserCollectionFactory $userCollectionFactory,
        Address $addressModel,
        Info $vendorModel,
        Data $dataHelper
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->addressCollection = $addressCollectionFactory;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->addressModel = $addressModel;
        $this->vendorModel = $vendorModel;
        $this->dataHelper = $dataHelper;

        parent::__construct($context);
    }

    /**
     * Save Vendor Addresses.
     *
     * @param $vendorId
     * @param $data
     */
    protected function _saveAddress($vendorId, $data)
    {
        $addressData = [
            Address::ADDRESS_TYPE_BILLING           => $data[Address::ADDRESS_TYPE_BILLING],
            Address::ADDRESS_TYPE_SHIPPING          => $data[Address::ADDRESS_TYPE_SHIPPING],
            Address::ADDRESS_TYPE_CUSTOMER_SERVICE  => $data[Address::ADDRESS_TYPE_CUSTOMER_SERVICE],
        ];

        // @codingStandardsIgnoreStart
        $collection = $this->addressCollection->create();
        $addresses = $collection->addFieldToFilter('vendor_id', $vendorId)->getItems();
        if (!$addresses) {
            foreach ($addressData as $key => $addressDatum) {
                $model = $this->_objectManager->create(Address::class);
                $model->setData($addressDatum);
                $model->setVendorId($vendorId);
                $model->setType($key);
                $model->save();
            }
        } else {
            foreach ($addresses as $address) {
                $address->setData($addressData[$address->getType()]);
                $address->save();
            }
        }
        // @codingStandardsIgnoreEnd
    }

    /**
     * Save Associated vendor users.
     *
     * @param $vendorId
     * @param $assocVendorUsersIds
     */
    protected function _saveAssocUsers($vendorId, $assocVendorUsersIds)
    {
        $usersIds = [];
        if ($assocVendorUsersIds) {
            $usersIds = json_decode($assocVendorUsersIds, true);
        }

        $collection = $this->userCollectionFactory->create();
        $assocVendorUsers = $collection->addFieldToFilter(
            [
                'user_id',
                'assoc_vendor_id',
            ],
            [
                ['in' => $usersIds],
                ["like" => '%"' . $vendorId . '"%'],
            ]
        );

        $adminData = [];
        foreach ($assocVendorUsers as $user) {
            $userIds = json_decode($user->getAssocVendorId());
            if (in_array($user->getId(), $usersIds)) {
                $userIds = !empty($userIds) ? array_unique(array_merge($userIds, [$vendorId])) : [$vendorId];
            } else {
                $userIds = !empty($userIds) ? array_diff($userIds, [$vendorId]) : null;
            }
            $adminData[] = ['user_id' => $user->getId(), 'assoc_vendor_id' => json_encode($userIds)];
        }

        if (!empty($adminData)) {
            $this->dataHelper->bulkUpdate($assocVendorUsers->getResource(), $adminData);
        }
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $postData = $this->getRequest()->getPostValue();
        $vendor = $postData['mageinn_vendor'];
        $addresses = $postData['vendor_address'];
        $settings = $postData['vendor_settings'];
        $batchExport = $postData['batch_export'];
        $batchImport = $postData['batch_import'];
        $assocUsers = isset($postData['vendor_users']) ? $postData['vendor_users'] : null;
        $info = $vendor[Info::VENDOR_DATA_INFORMATION];
        if ($info) {
            $id = isset($info['entity_id']) ? $info['entity_id'] : null;
            $info['same_as_billing'] = $addresses[Address::ADDRESS_TYPE_SHIPPING]['same_as_billing'];
            $info = array_merge(
                $info,
                $settings[Info::VENDOR_DATA_SETTINGS],
                $batchExport[Info::VENDOR_BATCH_EXPORT_GENERAL],
                $batchImport[Info::VENDOR_BATCH_IMPORT_GENERAL]
            );

            if (empty($info['entity_id'])) {
                $info['entity_id'] = null;
            }

            /** @var \Mageinn\Vendor\Model\Info $model */
            $model = $this->vendorModel->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This vendor no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($info);

            try {
                $model->save();
                $id = $id ? $id : $model->getId();
                $this->_saveAddress($id, $addresses);
                if ($assocUsers) {
                    $this->_saveAssocUsers($id, $assocUsers);
                }

                $this->messageManager->addSuccess(__('You saved the vendor.'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the vendor.'));
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('entity_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
