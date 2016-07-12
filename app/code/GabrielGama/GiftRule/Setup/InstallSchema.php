<?php
/**
 * GabrielGama <http://gabrielgama.com>
 *
 * DISCLAIMER
 *
 * Don't change this file if you will upgrade your module in the future.
 *
 * @category      GabrielGama
 * @package       GabrielGama_GiftRule
 *
 * @author        Gabriel da Gama <me@gabrielgama.com>
 */
namespace GabrielGama\GiftRule\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

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
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'gabrielgama_giftrule'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('gabrielgama_giftrule')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Description'
        )->addColumn(
            'from_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true, 'default' => null],
            'From'
        )->addColumn(
            'to_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true, 'default' => null],
            'To'
        )->addColumn(
            'uses_per_customer',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Uses Per Customer'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Is Active'
        )->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Conditions Serialized'
        )->addColumn(
            'actions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Actions Serialized'
        )->addColumn(
            'gift_sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Gift Sku'
        )->addColumn(
            'gift_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Gift Qty'
        )->addColumn(
            'times_used',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Times Used'
        )->addIndex(
            $installer->getIdxName('gabrielgama_giftrule', ['is_active', 'to_date', 'from_date']),
            ['is_active', 'to_date', 'from_date']
        )->setComment(
            'Giftrule'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'gabrielgama_giftrule_customer'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('gabrielgama_giftrule_customer')
        )->addColumn(
            'rule_customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Customer Id'
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Rule Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Customer Id'
        )->addColumn(
            'times_used',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Times Used'
        )->addIndex(
            $installer->getIdxName('gabrielgama_giftrule_customer', ['rule_id', 'customer_id']),
            ['rule_id', 'customer_id']
        )->addIndex(
            $installer->getIdxName('gabrielgama_giftrule_customer', ['customer_id', 'rule_id']),
            ['customer_id', 'rule_id']
        )->addForeignKey(
            $installer->getFkName('gabrielgama_giftrule_customer', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('gabrielgama_giftrule_customer', 'rule_id', 'gabrielgama_giftrule', 'rule_id'),
            'rule_id',
            $installer->getTable('gabrielgama_giftrule'),
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Giftrule Customer'
        );
        $installer->getConnection()->createTable($table);



        /**
         * Create table 'gabrielgama_giftrule_customer_group' if not exists. This table will be used instead of
         * column customer_group_ids of main catalog rules table
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('gabrielgama_giftrule_customer_group')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Group Id'
        )->addIndex(
            $installer->getIdxName('gabrielgama_giftrule_customer_group', ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $installer->getFkName('gabrielgama_giftrule_customer_group', 'rule_id', 'gabrielgama_giftrule', 'rule_id'),
            'rule_id',
            $installer->getTable('gabrielgama_giftrule'),
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'gabrielgama_giftrule_customer_group',
                'customer_group_id',
                'customer_group',
                'customer_group_id'
            ),
            'customer_group_id',
            $installer->getTable('customer_group'),
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Gift Rules To Customer Groups Relations'
        );

        $installer->getConnection()->createTable($table);

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'applied_gift_rule_ids',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Applied Gift Rule Ids',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'applied_gift_rule_ids',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Applied Gift Rule Ids',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_item'),
            'applied_gift_rule_ids',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Applied Gift Rule Ids',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_item'),
            'is_gift',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned' => true,
                'default' => 0,
                'comment' => 'Is Gift?',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address_item'),
            'applied_gift_rule_ids',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Applied Gift Rule Ids',
            ]
        );

        $installer->endSetup();

    }
}
