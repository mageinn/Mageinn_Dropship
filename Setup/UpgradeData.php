<?php
namespace Mageinn\Vendor\Setup;

use Mageinn\Vendor\Model\Region;
use Magento\Framework\Exception\LocalizedException;
use Mageinn\Vendor\Ui\DataProviders\Product\Form\Modifier\DeliveryCountry;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileDriver;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $moduleReader;

    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csvProcessor;

    /**
     * @var \Mageinn\Vendor\Helper\Data
     */
    private $coreHelper;

    /**
     * @var \Mageinn\Vendor\Model\ResourceModel\Region
     */
    private $regionResource;

    /**
     * @var array
     */
    private $regionFields = [
        Region::REGION_DATA_NAME,
        Region::REGION_DATA_COUNTRY,
        Region::REGION_DATA_CODE
    ];

    /**
     * Init
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\File\Csv $csvProcessor,
        \Mageinn\Vendor\Helper\Data $coreHelper,
        \Mageinn\Vendor\Model\ResourceModel\Region $regionResource
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->fileDriver = $file;
        $this->moduleReader = $moduleReader;
        $this->csvProcessor = $csvProcessor;
        $this->coreHelper = $coreHelper;
        $this->regionResource = $regionResource;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        if (version_compare($context->getVersion(), '0.0.18', '<')) {
            if ($installer->tableExists(Region::REGIONS_TABLE)) {
                // Do a cleanup.
                $this->regionResource->getConnection()->truncateTable(Region::REGIONS_TABLE);

                $path = $this->getDataFilePath('regions.csv');
                $data = $this->csvProcessor->getData($path);
                $data = $this->prepareCsvData($data, $this->regionFields);
                $this->coreHelper->bulkInsert($this->regionResource, $data);
            }
        }

        if (version_compare($context->getVersion(), '0.0.19', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->removeAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                DeliveryCountry::DELIVERY_COUNTRY_ATTR_CODE
            );

            $eavSetup->removeAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                DeliveryCountry::DELIVERY_COUNTRY_OVERRIDE_ATTR_CODE
            );

            $eavSetup->removeAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'shipping_ruleset'
            );

            $dcNote = 'If there are country(-ies) selected that do not match shipping ruleset then shipping rates'.
                ' will not be stored for them.';

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                DeliveryCountry::DELIVERY_COUNTRY_ATTR_CODE,
                [
                    'type' => 'varchar',
                    'label' => 'Delivery Country',
                    'note' => $dcNote,
                    'input' => 'multiselect',
                    'class' => 'delivery-country',
                    'source' => \Magento\Catalog\Model\Product\Attribute\Source\Countryofmanufacture::class,
                    'required' => false,
                    'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'unique' => false,
                    'apply_to' => '',
                    'group' => 'General',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => true,
                ]
            );

            $dcoNote = 'When ticked administrator can remove product from certain countries.';

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                DeliveryCountry::DELIVERY_COUNTRY_OVERRIDE_ATTR_CODE,
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'note' => $dcoNote,
                    'label' => 'Choose country manually',
                    'input' => 'boolean',
                    'group' => 'General',
                    'class' => 'delivery-country-override',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '0',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'shipping_ruleset',
                [
                    'type' => 'varchar',
                    'label' => 'Shipping Ruleset',
                    'input' => 'select',
                    'source' => \Mageinn\Vendor\Model\ShippingRate\Attribute\Source\ShippingRuleset::class,
                    'required' => false,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'unique' => false,
                    'apply_to' => '',
                    'group' => 'General',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
        }
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function getDataFilePath($fileName)
    {
        $etcPath = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            'Mageinn_Vendor'
        );

        return sprintf('%s/../Setup/Data/%s', $etcPath, $fileName);
    }

    /**
     * @param array $data
     * @param array $fields
     * @param bool $hasHeader
     * @return array
     * @throws LocalizedException
     */
    private function prepareCsvData($data, $fields, $hasHeader = false)
    {
        $fieldCount = count($fields);

        if ($hasHeader) {
            array_shift($data);
        }

        $result = [];
        foreach ($data as $rowIndex => $row) {
            // @codingStandardsIgnoreStart
            if (count($row) !== $fieldCount) {
                throw new LocalizedException(
                    __('Fields size does not match the number of columns found in data file.')
                );
            }
            // @codingStandardsIgnoreEnd

            foreach ($row as $index => $column) {
                $result[$rowIndex][$fields[$index]] = trim($column);
            }
        }

        return $result;
    }
}
