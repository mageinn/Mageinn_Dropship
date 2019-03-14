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
namespace Mageinn\Dropship\Setup;

/**
 * Class UpgradeData
 * @package Mageinn\Dropship\Setup
 */
class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * UpgradeData constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '0.0.30', '<')) {
            $dcNote = 'Please add Product Delivery Time.';

            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'product_delivery_time',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Product Delivery Time',
                    'note' => $dcNote,
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'wysiwyg_enabled' => false,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => 0,
                    'searchable' => false,
                    'filterable' => true,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );

            $dcNote = 'Please add Further details.';
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'further_details',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Further details',
                    'note' => $dcNote,
                    'input' => 'textarea',
                    'class' => '',
                    'source' => '',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_WEBSITE,
                    'wysiwyg_enabled' => true,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => 0,
                    'searchable' => false,
                    'filterable' => true,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );

            $dcNote = 'Please add Contact details.';
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'contact_details',
                [
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Contact Details',
                    'note' => $dcNote,
                    'input' => 'textarea',
                    'class' => '',
                    'source' => '',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_WEBSITE,
                    'wysiwyg_enabled' => true,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => 0,
                    'searchable' => false,
                    'filterable' => true,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.31', '<')) {
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attribute = $eavSetup->getAttribute($entityTypeId, 'product_delivery_time');
            if ($attribute) {
                $eavSetup->updateAttribute(
                    $entityTypeId,
                    $attribute['attribute_id'],
                    'is_filterable',
                    false
                );
            }

            $attribute = $eavSetup->getAttribute($entityTypeId, 'further_details');
            if ($attribute) {
                $eavSetup->updateAttribute(
                    $entityTypeId,
                    $attribute['attribute_id'],
                    'is_filterable',
                    false
                );
            }

            $attribute = $eavSetup->getAttribute($entityTypeId, 'contact_details');
            if ($attribute) {
                $eavSetup->updateAttribute(
                    $entityTypeId,
                    $attribute['attribute_id'],
                    'is_filterable',
                    false
                );
            }
        }
    }
}
