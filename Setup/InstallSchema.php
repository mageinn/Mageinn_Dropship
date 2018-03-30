<?php
namespace Mageinn\Dropship\Setup;

use \Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mageinn\Dropship\Model\Info;

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
                    'Dropship Id'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Dropship Name'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false, 'default' => '1'],
                    'Dropship Status'
                )
                ->addColumn(
                    'email',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Dropship Email'
                )
                ->addColumn(
                    'telephone',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Dropship Telephone'
                )
                ->addColumn(
                    'shipment_type',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Dropship Shipment Type'
                )
                ->addColumn(
                    'client_managed',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Dropship Is Client Managed'
                )
                ->addColumn(
                    'multiplier',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Dropship Multiplier'
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
                ->setComment('Dropship Information');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
