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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
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

        if (version_compare($context->getVersion(), '0.3.5', '<')) {
            $this->addMediaLibrary($setup);
            $this->addAdvertisement($setup);
        }

        if (version_compare($context->getVersion(), '0.4.4', '<')) {
            $this->addTokenManageTable($setup);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param OutputInterface      $output
     *
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup, OutputInterface $output)
    {
        $output->writeln('  |__ Add outlet table');
        $this->addOutletTable($setup);
        $output->writeln('  |__ Add register table');
        $this->addRegister($setup);
        $output->writeln('  |__ Add user order counter table');
        $this->addUserOrderCounterTable($setup);
        $output->writeln('  |__ Add receipt table');
        $this->addReceiptTable($setup);
        $this->dummyReceipt($setup);
        $output->writeln('  |__ Add role table');
        $this->createRoleTable($setup);
        $output->writeln('  |__ Add permission table');
        $this->createPermissionTable($setup);
        $this->definePermission($setup);
        $output->writeln('  |__ Add media library table');
        $this->addMediaLibrary($setup);
        $output->writeln('  |__ Add advertisement table');
        $this->addAdvertisement($setup);
        $output->writeln('  |__ Add token management table');
        $this->addTokenManageTable($setup);
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function addOutletTable(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_xretail_outlet'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_xretail_outlet')
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
            Table::TYPE_TEXT,
            255000,
            ['nullable' => false, 'default' => ''],
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
            255000,
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
            'Country Id'
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
        )->addColumn(
            'category_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Category ID'
        )->addColumn(
            'place_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Place ID Google Map'
        )->addColumn(
            'url',
            Table::TYPE_TEXT,
            25500,
            ['nullable' => false, 'default' => ''],
            'URL Google Map'
        )->addColumn(
            'lat',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Latitude Google Map'
        )->addColumn(
            'lng',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Longitude Google Map'
        )->addColumn(
            'location_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Location Id'
        )->addColumn(
            'allow_click_and_collect',
            Table::TYPE_SMALLINT,
            1,
            ['nullable' => false, 'default' => '1'],
            'Allow click and collect'
        )->addColumn(
            'allow_out_of_stock',
            Table::TYPE_SMALLINT,
            1,
            ['nullable' => false, 'default' => '1'],
            'Allow placing order with out of stock products'
        )->addColumn(
            'default_guest_customer_email',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false, 'default' => \SM\Customer\Helper\Data::DEFAULT_CUSTOMER_RETAIL_EMAIL],
            'Default guest customer email'
        );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function addRegister(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_xretail_register'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_xretail_register')
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
        $setup->getConnection()->createTable($table);

        $setup->getConnection()->addForeignKey(
            $setup->getFkName('id', 'outlet_id', $setup->getTable('sm_xretail_outlet'), 'id'),
            $setup->getTable('sm_xretail_register'),
            'outlet_id',
            $setup->getTable('sm_xretail_outlet'),
            'id',
            Table::ACTION_CASCADE
        );
        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function addUserOrderCounterTable(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_xretail_userordercounter'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_xretail_userordercounter')
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
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function addReceiptTable(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_xretail_receipt'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_xretail_receipt')
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
            255000,
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
            'Default receipt'
        )->addColumn(
            'day_of_week',
            Table::TYPE_TEXT,
            128,
            ['nullable' => false, 'default' => 'dddd'],
            'Day of Week'
        )->addColumn(
            'day_of_month',
            Table::TYPE_TEXT,
            128,
            ['nullable' => false, 'default' => 'Do'],
            'Day of Month'
        )->addColumn(
            'month',
            Table::TYPE_TEXT,
            128,
            ['nullable' => false, 'default' => 'MMM'],
            'Month'
        )->addColumn(
            'year',
            Table::TYPE_TEXT,
            128,
            ['nullable' => false, 'default' => 'YYYY'],
            'Year'
        )->addColumn(
            'time',
            Table::TYPE_TEXT,
            128,
            ['nullable' => false, 'default' => 'h:mm a'],
            'Time'
        )->addColumn(
            'insert_header_logo',
            Table::TYPE_TEXT,
            255000,
            ['nullable' => false],
            'Insert Header Logo'
        )->addColumn(
            'insert_footer_logo',
            Table::TYPE_TEXT,
            255000,
            ['nullable' => false],
            'Insert Footer Logo'
        )->addColumn(
            'display_custom_tax',
            Table::TYPE_INTEGER,
            3,
            ['nullable' => true, 'default' => 0],
            'Is Display Custom Tax'
        )->addColumn(
            'custom_tax_multiplier',
            Table::TYPE_DECIMAL,
            '12,9',
            ['nullable' => true, 'default' => 0],
            'Custom Tax Multiplier'
        )->addColumn(
            'paper_size',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Paper Size'
        )->addColumn(
            'style_customer_info',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Style For Customer Info'
        )->addColumn(
            'store_info',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Store Info'
        )->addColumn(
            'store_phone',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Store Phone'
        )->addColumn(
            'store_website',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Store Website'
        )->addColumn(
            'enable_terms_and_conditions',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Terms and Conditions of Sale'
        )->addColumn(
            'terms_and_conditions',
            Table::TYPE_TEXT,
            255000,
            ['nullable' => false,],
            'Terms and Conditions Content'
        )->addColumn(
            'enable_customer_signature',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'Customer Signature'
        )->addColumn(
            'custom_tax_label',
            Table::TYPE_TEXT,
            20,
            ['nullable' => true, 'default' => 'Tax'],
            'Custom Tax Label'
        )->addColumn(
            'sm_xretail_receipt',
            Table::TYPE_INTEGER,
            3,
            ['nullable' => true, 'default' => 0],
            'Display Two Languages'
        )->addColumn(
            'second_language',
            Table::TYPE_TEXT,
            3,
            ['nullable' => true, 'default' => 'en'],
            'Second Language'
        )->addColumn(
            'order_info',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false,],
            'Order Info'
        );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function createRoleTable(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_role'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_role')
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
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function createPermissionTable(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_permission'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_permission')
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
        $setup->getConnection()->createTable($table);
        $setup->getConnection()->addForeignKey(
            $setup->getFkName('id', 'role_id', $setup->getTable('sm_role'), 'id'),
            $setup->getTable('sm_permission'),
            'role_id',
            $setup->getTable('sm_role'),
            'id',
            Table::ACTION_CASCADE
        );
        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function definePermission(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        $roleTable = $setup->getTable('sm_role');
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
                ],
            ]
        );

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    protected function dummyReceipt(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

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
                'is_default',
            ],
            [
                [
                    "customer_info"       => "1",
                    "order_info"          => json_encode(
                        [
                            "shipping_address"  => true,
                            "sales_person"      => true,
                            "discount_shipment" => true,
                        ],
                        true
                    ),
                    "row_total_incl_tax"  => true,
                    "logo_image_status"   => true,
                    "footer_image_status" => true,
                    "subtotal_incl_tax"   => true,
                    "header"              => "<h2>ConnectPOS</h2>",
                    "footer"              => "Thank you for shopping!",
                    "enable_barcode"      => true,
                    "barcode_symbology"   => "CODE128",
                    "enable_power_text"   => true,
                    "name"                => "ConnectPOS default receipt",
                    "is_default"          => true,
                ],
            ]
        );

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function addMediaLibrary(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_media_library'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_media_library')
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
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function addAdvertisement(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_advertisement'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_advertisement')
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
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    protected function addTokenManageTable(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists($setup->getTable('sm_token'))) {
            $setup->endSetup();

            return;
        }

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sm_token')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'token',
            Table::TYPE_TEXT,
            25500,
            ['nullable' => false],
            'Token'
        )->addColumn(
            'user_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false,],
            'User Id'
        )->addColumn(
            'expired_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Expired At'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }

}
