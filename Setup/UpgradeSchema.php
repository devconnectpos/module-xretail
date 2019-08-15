<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\XRetail\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface {
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '0.0.8', '<')) {
            $this->addOutletTable($setup);
            $this->addRegister($setup);
        }
        if (version_compare($context->getVersion(), '0.1.0', '<')) {
            $this->addReceiptTable($setup);
        }
        if (version_compare($context->getVersion(), '0.1.3', '<')) {
            $this->addUserOrderCounterTable($setup);
        }
        if (version_compare($context->getVersion(), '0.1.2', '<')) {
            $this->createRoleTable($setup);
            $this->createPermissionTable($setup);
            $this->definePermission($setup);
        }
        if (version_compare($context->getVersion(), '0.1.6', '<')) {
            $this->addReceiptTable($setup);
            $this->dummyReceipt($setup);
        }
        if (version_compare($context->getVersion(), '0.1.7', '<')) {
            $this->dummyReceipt($setup);
        }
        if (version_compare($context->getVersion(), '0.2.3', '<')) {
            $this->modifyColumnHeaderReceipt($setup);
        }
        if (version_compare($context->getVersion(), '0.2.4', '<')) {
            $this->addNewColumnMapForOutlet($setup);
        }
        if (version_compare($context->getVersion(), '0.2.5', '<')) {
            $this->addNewColumnCustomDateTimeReceipt($setup);
            $this->updateDefaultDateTimeReceipt($setup);
        }
        if (version_compare($context->getVersion(), '0.2.6', '<')) {
            $this->addMapInfoOutlet($setup);
        }
        if (version_compare($context->getVersion(), '0.3.0', '<')) {
            $this->addCategoryOutletTable($setup);
        }
        if (version_compare($context->getVersion(), '0.3.1', '<')) {
            $this->modifyColumnWarehouseOutlet($setup);
        }
        if (version_compare($context->getVersion(), '0.3.5', '<')) {
            $this->addMediaLibrary($setup);
            $this->addAdvertisement($setup);
        }
        if (version_compare($context->getVersion(), '0.3.6', '<')) {
            $this->modifyOutletColumn($setup);
        }
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    protected function addCategoryOutletTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $installer->getConnection()->dropColumn($installer->getTable('sm_xretail_outlet'), 'category_id');
        $installer->getConnection()->addColumn(
            $installer->getTable('sm_xretail_outlet'),
            'category_id',
            [
                'type'    => Table::TYPE_INTEGER,
                'comment' => 'category id',
            ]
        );

        $installer->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function addOutletTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $setup->getConnection()->dropTable($setup->getTable('sm_xretail_outlet'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('sm_xretail_outlet')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Demo Title'
        )->addColumn(
            'warehouse_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'WareHouse ID'
        )->addColumn(
            'store_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Store ID'
        )->addColumn(
            'cashier_ids',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Cashier Ids'
        )->addColumn(
            'enable_guest_checkout',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1',],
            'Enable Guest Checkout'
        )->addColumn(
            'tax_calculation_based_on',
            Table::TYPE_TEXT,
            20,
            ['nullable' => false,],
            'Tax Calculation Based On'
        )->addColumn(
            'paper_receipt_template_id',
            Table::TYPE_TEXT,
            5,
            ['nullable' => false,],
            "Paper Receipt's template"
        )->addColumn(
            'street',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Street'
        )->addColumn(
            'city',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'City'
        )->addColumn(
            'country_id',
            Table::TYPE_TEXT,
            10,
            ['nullable' => false,],
            'Region Id'
        )->addColumn(
            'region_id',
            Table::TYPE_TEXT,
            10,
            ['nullable' => false,],
            'Region Id'
        )->addColumn(
            'postcode',
            Table::TYPE_TEXT,
            10,
            ['nullable' => false,],
            'Postcode'
        )->addColumn(
            'telephone',
            Table::TYPE_TEXT,
            40,
            ['nullable' => false,],
            'Telephone'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
            'Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
            'Modification Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1',],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function addRegister(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $setup->getConnection()->dropTable($setup->getTable('sm_xretail_register'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('sm_xretail_register')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'outlet_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Demo Title'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
            'Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
            'Modification Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1',],
            'Is Active'
        )->addColumn(
            'is_print_receipt',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1',],
            'Is Always Print Receipt'
        );
        $installer->getConnection()->createTable($table);

        $installer->getConnection()->addForeignKey(
            $installer->getFkName('id', 'outlet_id', $installer->getTable('sm_xretail_outlet'), 'id'),
            $installer->getTable('sm_xretail_register'),
            'outlet_id',
            $installer->getTable('sm_xretail_outlet'),
            'id',
            Table::ACTION_CASCADE
        );

        $installer->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function addUserOrderCounterTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $setup->getConnection()->dropTable($setup->getTable('sm_xretail_userordercounter'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('sm_xretail_userordercounter')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'user_id',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'User ID'
        )->addColumn(
            'outlet_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Outlet ID'
        )->addColumn(
            'register_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Outlet ID'
        )->addColumn(
            'order_count',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Order Count'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function addReceiptTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $setup->getConnection()->dropTable($setup->getTable('sm_xretail_receipt'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('sm_xretail_receipt')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'logo_image_status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false,],
            'Logo image status'
        )->addColumn(
            'logo_url',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false,],
            'Logo Url'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Name'
        )->addColumn(
            'footer_image_status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false,],
            'Logo image status'
        )->addColumn(
            'footer_url',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false,],
            'Footer Url'
        )->addColumn(
            'header',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Header'
        )->addColumn(
            'footer',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Footer'
        )->addColumn(
            'customer_info',
            Table::TYPE_TEXT,
            5,
            ['nullable' => false,],
            'Customer Info'
        )->addColumn(
            'font_type',
            Table::TYPE_TEXT,
            5,
            ['nullable' => false,],
            'Font Type'
        )->addColumn(
            'barcode_symbology',
            Table::TYPE_TEXT,
            20,
            ['nullable' => false,],
            'Barcode Symbology'
        )->addColumn(
            'row_total_incl_tax',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false,],
            'Row total Incl tax'
        )->addColumn(
            'subtotal_incl_tax',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false,],
            'Subtotal Incl Tax'
        )->addColumn(
            'enable_barcode',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false,],
            'Enable Barcode'
        )->addColumn(
            'enable_power_text',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false,],
            'Enable Power text'
        )->addColumn(
            'order_info',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Order Info'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
            'Creation Time'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
            'Modification Time'
        )->addColumn(
            'is_default',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false,],
            'default receipt'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function createRoleTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        //START table setup
        $table = $installer->getConnection()->newTable(
            $installer->getTable('sm_role')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Demo Title'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
            'Creation Time'
        )->addColumn(
            'updated_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
            'Modification Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1',],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function createPermissionTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        //START table setup
        $table = $installer->getConnection()->newTable(
            $installer->getTable('sm_permission')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'role_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true,],
            'Role ID'
        )->addColumn(
            'group',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false,],
            'Group'
        )->addColumn(
            'permission',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false,],
            'Permission'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT,],
            'Creation Time'
        )->addColumn(
            'updated_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE,],
            'Modification Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1',],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);
        $installer->getConnection()->addForeignKey(
            $installer->getFkName('id', 'role_id', $installer->getTable('sm_role'), 'id'),
            $installer->getTable('sm_permission'),
            'role_id',
            $installer->getTable('sm_role'),
            'id',
            Table::ACTION_CASCADE
        );
        $installer->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function definePermission(SchemaSetupInterface $setup)
    {
        $roleTable       = $setup->getTable('sm_role');
        $permissionTable = $setup->getTable('sm_permission');
        $setup->getConnection()->truncateTable($roleTable);
        $setup->getConnection()->truncateTable($permissionTable);

        $setup->getConnection()->insertArray(
            $roleTable,
            [
                'name',
            ],
            [
                [
                    'name' => "Admin",
                ],
                [
                    'name' => "Manager",
                ],
                [
                    'name' => "Accountant",
                ],
                [
                    'name' => "Cashier",
                ]
            ]
        );
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    protected function dummyReceipt(SchemaSetupInterface $setup)
    {
        $receiptTable = $setup->getTable('sm_xretail_receipt');
        $setup->getConnection()->truncateTable($receiptTable);
        $setup->getConnection()->insertArray(
            $receiptTable,
            [
                'customer_info',
                'order_info',
                'row_total_incl_tax',
                "logo_image_status",
                "footer_image_status",
                'subtotal_incl_tax',
                'header',
                'footer',
                'enable_barcode',
                'barcode_symbology',
                'enable_power_text',
                'name',
                'is_default'
            ],
            [
                [
                    "customer_info"      => "1",
                    "order_info"         => json_encode(
                        [
                            "shipping_address"  => true,
                            "sales_person"      => true,
                            "discount_shipment" => true
                        ],
                        true),
                    "row_total_incl_tax" => true,
                    "logo_image_status" => true,
                    "footer_image_status" =>true,
                    "subtotal_incl_tax"  => true,
                    "header"             => "<h2>X-POS</h2>",
                    "footer"             => "Thank you for shopping!",
                    "enable_barcode"     => true,
                    "barcode_symbology"  => "CODE128",
                    "enable_power_text"  => true,
                    "name"               => "X-Retail default receipt",
                    "is_default"         => true,
                ]
            ]
        );
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    protected function modifyColumnHeaderReceipt(SchemaSetupInterface $setup)
    {
        $receiptTable = $setup->getTable('sm_xretail_receipt');
        $setup->startSetup();

        $setup->getConnection()->changeColumn(
            $setup->getTable($receiptTable),
            'header',
            'header',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255000,
                ['nullable' => false,],
                'Header'
            ]
        );

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    protected function addNewColumnMapForOutlet(SchemaSetupInterface $setup)
    {
        $installer = $setup;

        $installer->getConnection()->addColumn(
            $installer->getTable('sm_xretail_outlet'),
            'place_id',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 25500,
                'nullable' => false,
                'comment' => 'Place ID Google Map'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sm_xretail_outlet'),
            'url',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 25500,
                'nullable' => false,
                'comment' => 'URL Google Map'
            ]
        );

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    protected function addNewColumnCustomDateTimeReceipt(SchemaSetupInterface $setup)
    {
        $installer = $setup;

        $installer->getConnection()->addColumn(
            $installer->getTable('sm_xretail_receipt'),
            'day_of_week',
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 25500,
                'nullable' => false,
                'comment'  => 'Day of Week'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sm_xretail_receipt'),
            'day_of_month',
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 25500,
                'nullable' => false,
                'comment'  => 'Day of Month'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sm_xretail_receipt'),
            'month',
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 25500,
                'nullable' => false,
                'comment'  => 'Month'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sm_xretail_receipt'),
            'year',
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 25500,
                'nullable' => false,
                'comment'  => 'Year'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sm_xretail_receipt'),
            'time',
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 25500,
                'nullable' => false,
                'comment'  => 'Time'
            ]
        );

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    protected function updateDefaultDateTimeReceipt(SchemaSetupInterface $setup)
    {
        $installer = $setup;

        $installer->getConnection()->update(
            $installer->getTable('sm_xretail_receipt'),
            [
                'day_of_week'  => 'dddd',
                'day_of_month' => 'Do',
                'month'        => 'MMM',
                'year'         => 'YYYY',
                'time'         => 'h:mm a',
            ],
            ['day_of_week = ?' => null]
        );
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    protected function addMapInfoOutlet(SchemaSetupInterface $setup)
    {
        $installer = $setup;

        $installer->getConnection()->addColumn(
            $installer->getTable('sm_xretail_outlet'),
            'lat',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255000,
                'nullable' => false,
                'comment' => 'Latitude Google Map'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sm_xretail_outlet'),
            'lng',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255000,
                'nullable' => false,
                'comment' => 'Longitude Google Map'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sm_xretail_outlet'),
            'allow_click_and_collect',
            [
                'type' =>  Table::TYPE_SMALLINT,
                'length' => 1,
                'nullable' => false,
                'default' => '1',
                'comment' => 'Allow click and collect'
            ]
        );

        $setup->endSetup();
    }

    protected function modifyColumnWarehouseOutlet(SchemaSetupInterface $setup)
    {
        $outletTable = $setup->getTable('sm_xretail_outlet');
        $setup->startSetup();

        $setup->getConnection()->changeColumn(
            $setup->getTable($outletTable),
            'warehouse_id',
            'warehouse_id',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255000,
                ['nullable' => false],
                'Warehouse ID'
            ]
        );

        $setup->endSetup();
    }

    protected function addMediaLibrary(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $setup->getConnection()->dropTable($setup->getTable('sm_media_library'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('sm_media_library')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Demo Title'
        )->addColumn(
            'url',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Url'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Media Type'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creation Time'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Modification Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

    protected function addAdvertisement(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $setup->getConnection()->dropTable($setup->getTable('sm_advertisement'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('sm_advertisement')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
            'Entity ID'
        )->addColumn(
            'list_media',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'List Media'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Demo Title'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            25500,
            ['nullable' => true],
            'Description'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Type'
        )->addColumn(
            'duration',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Duration'
        )->addColumn(
            'priority',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Priority'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creation Time'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Modification Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

    protected function modifyOutletColumn(SchemaSetupInterface $setup) {
        $installer = $setup;
        $installer->startSetup();

        $installer
            ->getConnection()
            ->modifyColumn(
                $installer->getTable('sm_xretail_outlet'),
                'lat',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => 255000,
                    'nullable' => false,
                    'comment'  => 'Latitude Google Map'
                ]
            );

        $installer
            ->getConnection()
            ->modifyColumn(
                $installer->getTable('sm_xretail_outlet'),
                'lng',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => 255000,
                    'nullable' => false,
                    'comment'  => 'Longitude Google Map'
                ]
            );

        $installer->endSetup();
    }
}
