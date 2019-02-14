<?php
namespace Iredeem\Vendor\Setup;

use \Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\DB\Adapter\AdapterInterface;
use \Magento\Framework\Setup\UpgradeSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Iredeem\Vendor\Model\Info;
use \Iredeem\Vendor\Model\Address;
use \Iredeem\Vendor\Model\Batch;
use \Iredeem\Vendor\Model\BatchRow;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @codingStandardsIgnoreStart
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.2') < 0) {
            if ($setup->tableExists(Info::VENDOR_INFO_TABLE)) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(Info::VENDOR_INFO_TABLE),
                    'same_as_billing',
                    [
                        'type' => Table::TYPE_BOOLEAN,
                        'nullable' => false,
                        'default' => 1,
                        'comment' => 'Same as Billing'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '0.0.3') < 0) {
            /**
             * Create table 'iredeem_vendor_address'
             */
            if (!$setup->tableExists(Address::VENDOR_ADDRESS_TABLE)) {
                $table = $setup->getConnection()
                    ->newTable($setup->getTable(Address::VENDOR_ADDRESS_TABLE))
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'Vendor Address Id'
                    )
                    ->addColumn(
                        'vendor_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false],
                        'Vendor Address Id'
                    )
                    ->addColumn(
                        'type',
                        Table::TYPE_TEXT,
                        null,
                        ['nullable' => false],
                        'Vendor Address Type'
                    )
                    ->addColumn(
                        'contact_name',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Vendor Address Contact Name'
                    )
                    ->addColumn(
                        'street',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Vendor Address Street'
                    )
                    ->addColumn(
                        'city',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Vendor Address City'
                    )
                    ->addColumn(
                        'postal_code',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Vendor Address Postal Code'
                    )
                    ->addColumn(
                        'country',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Vendor Address Country'
                    )
                    ->addColumn(
                        'region',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Vendor Address State/Region'
                    )
                    ->addColumn(
                        'region_id',
                        Table::TYPE_INTEGER,
                        255,
                        ['unsigned' => true],
                        'Vendor Address State/Region ID'
                    )
                    ->addColumn(
                        'email',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Vendor Address Email'
                    )
                    ->addColumn(
                        'telephone',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Vendor Address Telephone'
                    )
                    ->addColumn(
                        'url_title',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Vendor Address URL Title'
                    )
                    ->addColumn(
                        'url',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Vendor Address URL'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $setup->getTable(Address::VENDOR_ADDRESS_TABLE),
                            ['entity_id'],
                            AdapterInterface::INDEX_TYPE_INDEX
                        ),
                        ['entity_id'],
                        AdapterInterface::INDEX_TYPE_INDEX
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            Address::VENDOR_ADDRESS_TABLE,
                            'entity_id',
                            Info::VENDOR_INFO_TABLE,
                            'entity_id'
                        ),
                        'vendor_id',
                        $setup->getTable(Info::VENDOR_INFO_TABLE),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Vendor Address');
                $setup->getConnection()->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), '0.0.4') < 0) {
            if ($setup->tableExists(Info::VENDOR_INFO_TABLE)) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(Info::VENDOR_INFO_TABLE),
                    'currency',
                    [
                        'type' => Table::TYPE_TEXT,
                        'comment' => 'Currency'
                    ]
                );
                $setup->getConnection()->addColumn(
                    $setup->getTable(Info::VENDOR_INFO_TABLE),
                    'notify_order',
                    [
                        'type' => Table::TYPE_BOOLEAN,
                        'nullable' => false,
                        'default' => 0,
                        'comment' => 'Notify Vendor on Order'
                    ]
                );
                $setup->getConnection()->addColumn(
                    $setup->getTable(Info::VENDOR_INFO_TABLE),
                    'notify_order_email',
                    [
                        'type' => Table::TYPE_TEXT,
                        'comment' => 'Email for order notification'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '0.0.5') < 0) {
            if ($setup->tableExists(Info::VENDOR_INFO_TABLE)) {
                $setup->getConnection()->dropColumn(
                    $setup->getTable(Info::VENDOR_INFO_TABLE),
                    'shipment_type'
                );
            }
        }

        if (version_compare($context->getVersion(), '0.0.6') < 0) {
            if ($setup->tableExists('quote_item')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('quote_item'),
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Vendor ID'
                    ]
                );
            }
            if ($setup->tableExists('sales_order_item')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('sales_order_item'),
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Vendor ID'
                    ]
                );
            }
            if ($setup->tableExists('sales_shipment')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('sales_shipment'),
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Vendor ID'
                    ]
                );
            }
            if ($setup->tableExists('sales_shipment_item')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('sales_shipment_item'),
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Vendor ID'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '0.0.7') < 0) {
            if ($setup->tableExists('sales_shipment')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('sales_shipment'),
                    'dropship_status',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Drop Shipment Status'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '0.0.8') < 0) {
            if ($setup->tableExists(Info::VENDOR_INFO_TABLE)) {
                $tableName = $setup->getTable(Info::VENDOR_INFO_TABLE);
                $connection = $setup->getConnection();

                $columns = [
                    'batch_export_enabled' => [
                        'type' => Table::TYPE_BOOLEAN,
                        'nullable' => false,
                        'default' => 0,
                        'comment' => 'Batch Export Enabled'
                    ],
                    'batch_export_shipment_status' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Batch Export Change Shipment Status'
                    ],
                    'batch_export_schedule' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Batch Export Cron Schedule'
                    ],
                    'batch_export_destination' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Batch Export Destination'
                    ],
                    'batch_export_headings' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Batch Export File headings'
                    ],
                    'batch_export_values' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Batch Export Values'
                    ]
                ];

                foreach ($columns as $columnName => $columnData) {
                    $connection->addColumn($tableName, $columnName, $columnData);
                }
            }
        }

        if (version_compare($context->getVersion(), '0.0.9') < 0) {
            if ($setup->tableExists(Info::VENDOR_INFO_TABLE)) {
                $tableName = $setup->getTable(Info::VENDOR_INFO_TABLE);
                $connection = $setup->getConnection();

                $columns = [
                    'batch_import_enabled' => [
                        'type' => Table::TYPE_BOOLEAN,
                        'nullable' => false,
                        'default' => 0,
                        'comment' => 'Batch Import Enabled'
                    ],
                    'batch_import_schedule' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Batch Import Cron Schedule'
                    ],
                    'batch_import_source' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Batch Import Source'
                    ],
                    'batch_import_file_data' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Values in import file'
                    ],
                    'batch_import_delimiter' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Field delimiter'
                    ]
                ];

                foreach ($columns as $columnName => $columnData) {
                    $connection->addColumn($tableName, $columnName, $columnData);
                }
            }
        }

        if (version_compare($context->getVersion(), '0.0.10') < 0) {
            /**
             * Create table 'iredeem_dropship_batch'
             */
            if (!$setup->tableExists(Batch::TABLE_DROPSHIP_BATCH)) {
                $table = $setup->getConnection()
                    ->newTable($setup->getTable(Batch::TABLE_DROPSHIP_BATCH))
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
                    )->addColumn(
                        'vendor_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false]
                    )->addColumn(
                        'type',
                        Table::TYPE_TEXT,
                        20,
                        ['nullable' => false]
                    )->addColumn(
                        'status',
                        Table::TYPE_TEXT,
                        20,
                        ['nullable' => false]
                    )->addColumn(
                        'rows_text',
                        Table::TYPE_TEXT,
                        255,
                        []
                    )->addColumn(
                        'num_rows',
                        Table::TYPE_SMALLINT,
                        null,
                        []
                    )->addColumn(
                        'created_at',
                        Table::TYPE_DATETIME,
                        null,
                        []
                    )->addColumn(
                        'scheduled_at',
                        Table::TYPE_DATETIME,
                        null,
                        []
                    )->addColumn(
                        'updated_at',
                        Table::TYPE_DATETIME,
                        null,
                        []
                    )->addColumn(
                        'notes',
                        Table::TYPE_TEXT,
                        255,
                        []
                    )->addColumn(
                        'error_info',
                        Table::TYPE_TEXT,
                        255,
                        []
                    )->addColumn(
                        'adapter_type',
                        Table::TYPE_TEXT,
                        255,
                        []
                    )->addIndex(
                        $setup->getIdxName(
                            $setup->getTable(Batch::TABLE_DROPSHIP_BATCH),
                            ['entity_id'],
                            AdapterInterface::INDEX_TYPE_INDEX
                        ),
                        ['entity_id'],
                        AdapterInterface::INDEX_TYPE_INDEX
                    )->addForeignKey(
                        $setup->getFkName(
                            Batch::TABLE_DROPSHIP_BATCH,
                            'entity_id',
                            Info::VENDOR_INFO_TABLE,
                            'entity_id'
                        ),
                        'vendor_id',
                        $setup->getTable(Info::VENDOR_INFO_TABLE),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )->setComment('Vendor Dropship Batch');
                $setup->getConnection()->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), '0.0.11') < 0) {
            if ($setup->tableExists('sales_shipment_grid')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('sales_shipment_grid'),
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Vendor ID'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '0.0.12') < 0) {
            if ($setup->tableExists('sales_shipment')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('sales_shipment'),
                    'shipping_date',
                    [
                        'type' => Table::TYPE_DATETIME,
                        'nullable' => false,
                        'comment' => 'Drop Shipment Date'
                    ]
                );
            }
        }

        // Need to change the types of the columns to simple text because there is the possibility
        // that the export file of the error may contain more than 255 chars
        if (version_compare($context->getVersion(), '0.0.13') < 0) {
            if ($setup->tableExists('iredeem_dropship_batch')) {
                $setup->getConnection()->modifyColumn(
                    $setup->getTable('iredeem_dropship_batch'),
                    'rows_text',
                    [
                        'type' => Table::TYPE_TEXT,
                        'comment' => 'Rows Text'
                    ]
                )->modifyColumn(
                    $setup->getTable('iredeem_dropship_batch'),
                    'error_info',
                    [
                        'type' => Table::TYPE_TEXT,
                        'comment' => 'Error Info'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '0.0.14') < 0) {
            if ($setup->tableExists('admin_user')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('admin_user'),
                    'assoc_vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'default' => '0',
                        'nullable' => false,
                        'comment' => 'Associated Vendor ID'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '0.0.15') < 0) {
            /**
             * Create table 'iredeem_dropship_batch_row'
             */
            if (!$setup->tableExists(BatchRow::TABLE_DROPSHIP_BATCH_ROW)) {
                $table = $setup->getConnection()
                    ->newTable($setup->getTable(BatchRow::TABLE_DROPSHIP_BATCH_ROW))
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
                    )->addColumn(
                        'batch_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false]
                    )->addColumn(
                        'order_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true]
                    )->addColumn(
                        'shipment_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true]
                    )->addColumn(
                        'item_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true]
                    )->addColumn(
                        'track_id',
                        Table::TYPE_TEXT,
                        50,
                        ['unsigned' => true]
                    )->addColumn(
                        'order_increment_id',
                        Table::TYPE_TEXT,
                        50,
                        []
                    )->addColumn(
                        'shipment_increment_id',
                        Table::TYPE_TEXT,
                        50,
                        []
                    )->addColumn(
                        'item_sku',
                        Table::TYPE_TEXT,
                        50,
                        []
                    )->addColumn(
                        'tracking_id',
                        Table::TYPE_TEXT,
                        50,
                        []
                    )->addColumn(
                        'has_error',
                        Table::TYPE_BOOLEAN,
                        null,
                        []
                    )->addColumn(
                        'error_info',
                        Table::TYPE_TEXT,
                        []
                    )->addColumn(
                        'row_json',
                        Table::TYPE_TEXT,
                        []
                    )->addIndex(
                        $setup->getIdxName(
                            $setup->getTable(BatchRow::TABLE_DROPSHIP_BATCH_ROW),
                            ['entity_id'],
                            AdapterInterface::INDEX_TYPE_INDEX
                        ),
                        ['entity_id'],
                        AdapterInterface::INDEX_TYPE_INDEX
                    )->addForeignKey(
                        $setup->getFkName(
                            BatchRow::TABLE_DROPSHIP_BATCH_ROW,
                            'entity_id',
                            Batch::TABLE_DROPSHIP_BATCH,
                            'entity_id'
                        ),
                        'batch_id',
                        $setup->getTable(Batch::TABLE_DROPSHIP_BATCH),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )->addForeignKey(
                        $setup->getFkName(
                            Batch::TABLE_DROPSHIP_BATCH,
                            'entity_id',
                            'sales_shipment',
                            'entity_id'
                        ),
                        'shipment_id',
                        $setup->getTable('sales_shipment'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )->addForeignKey(
                        $setup->getFkName(
                            Batch::TABLE_DROPSHIP_BATCH,
                            'entity_id',
                            'sales_order',
                            'entity_id'
                        ),
                        'order_id',
                        $setup->getTable('sales_order'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )->setComment('Vendor Dropship Batch Row');
                $setup->getConnection()->createTable($table);
            }

            if ($setup->tableExists(Batch::TABLE_DROPSHIP_BATCH)) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(Batch::TABLE_DROPSHIP_BATCH),
                    'file_path',
                    [
                        'type' => Table::TYPE_TEXT,
                        'comment' => 'File Path for Import and Export',
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '0.0.16') < 0) {
            if ($setup->tableExists('sales_shipment_comment')) {
                $setup->getConnection()
                    ->addColumn(
                        'sales_shipment_comment',
                        'status',
                        [
                            'type' => Table::TYPE_TEXT,
                            'nullable' => true,
                            'default' => '',
                            'comment' => 'Shippment status'
                        ]
                    );
            }

            if ($setup->tableExists('sales_shipment_grid')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('sales_shipment_grid'),
                    'dropship_status',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Dropship Status'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '0.0.17') < 0) {
            if ($setup->tableExists('iredeem_dropship_batch')) {
                $setup->getConnection()->modifyColumn(
                    $setup->getTable('admin_user'),
                    'assoc_vendor_id',
                    [
                        'type' => Table::TYPE_TEXT,
                        'comment' => 'Associated Vendor IDs'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '0.0.32') < 0) {
            if ($setup->tableExists(Info::VENDOR_INFO_TABLE)) {
                $tableName = $setup->getTable(Info::VENDOR_INFO_TABLE);
                $connection = $setup->getConnection();

                $columns = [
                    'batch_export_private_key' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Batch SFTP Private Key'
                    ]
                ];

                foreach ($columns as $columnName => $columnData) {
                    $connection->addColumn($tableName, $columnName, $columnData);
                }
            }
        }

        $setup->endSetup();
    }
    // @codingStandardsIgnoreEnd
}
