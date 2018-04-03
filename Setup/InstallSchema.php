<?php
namespace Mageinn\Vendor\Setup;

use \Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mageinn\Vendor\Model\Info;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $context->getVersion();
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'mageinn_vendor_information'
         */
        if (!$installer->tableExists(Info::VENDOR_INFO_TABLE)) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable(Info::VENDOR_INFO_TABLE))
                ->addColumn(
                    'entity_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Vendor Id'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Vendor Name'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false, 'default' => '1'],
                    'Vendor Status'
                )
                ->addColumn(
                    'email',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Vendor Email'
                )
                ->addColumn(
                    'telephone',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Vendor Telephone'
                )
                ->addColumn(
                    'shipment_type',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Vendor Shipment Type'
                )
                ->addColumn(
                    'client_managed',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Vendor Is Client Managed'
                )
                ->addColumn(
                    'multiplier',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Vendor Multiplier'
                )
                ->addIndex(
                    $setup->getIdxName(
                        $installer->getTable(Info::VENDOR_INFO_TABLE),
                        ['entity_id'],
                        AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    ['entity_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                )
                ->setComment('Vendor Information');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
