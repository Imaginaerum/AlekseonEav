<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace Alekseon\AlekseonEav\Setup;

use Magento\Framework\DB\Ddl\Table;

/**
 * Class SchemaSetup
 * @package Alekseon\AlekseonEav\Setup
 */
class EavSchemaSetup implements EavSchemaSetupInterface
{
    /**
     * @var \Magento\Framework\Setup\SchemaSetupInterface
     */
    private $setup;

    /**
     * EavSchemaSetup constructor.
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function __construct(
        \Magento\Framework\Setup\SchemaSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    /**
     * @return array
     */
    protected function getEntitiesConfig()
    {
        return [
            'varchar' => [
                'value_column' => [
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'options' => [],
                ],
                'table_description' => 'Alekseon EAV Entity Varchar',
            ],
            'int' => [
                'value_column' => [
                    'type' => Table::TYPE_INTEGER,
                    'size' => null,
                    'options' => [],
                ],
                'table_description' => 'Alekseon EAV Entity Int',
            ],
            'text' => [
                'value_column' => [
                    'type' => Table::TYPE_TEXT,
                    'size' => null,
                    'options' => [],
                ],
                'table_description' => 'Alekseon EAV Entity Text',
            ],
            'datetime' => [
                'value_column' => [
                    'type' => Table::TYPE_DATETIME,
                    'size' => null,
                    'options' => [],
                ],
                'table_description' => 'Alekseon EAV Entity Datetime',
            ],
        ];
    }

    /**
     * @param $attributeTableName
     * @param $eavEntityTablesPrefix
     * @param null $entitiesToCreate
     * @param null $entityTableName
     * IMPORTANT: set entity table name for foreign key only if
     * eav entities tables are created for specific entity,
     * set null if entities are used for more entities
     * @param string $entityTableIdField
     * @throws \Zend_Db_Exception
     */
    public function createFullEavStructure(
        $attributeTableName,
        $eavEntityTablesPrefix,
        $entitiesToCreate = null,
        $entityTableName = null,
        $entityTableIdField = 'entity_id'
    ) {
        $this->createEavAttributeTable($attributeTableName);
        $attributeOptionTableName = $attributeTableName . '_option';
        $this->createOptionTables($attributeTableName, $attributeOptionTableName);
        $this->createEavEntitiesTables(
            $attributeTableName,
            $eavEntityTablesPrefix,
            $entitiesToCreate,
            $entityTableName,
            $entityTableIdField
        );
        $attributeFrontendLabelsTableName = $attributeTableName . '_frontend_label';
        $this->createFrontendLabelsTable($attributeTableName, $attributeFrontendLabelsTableName);
    }

    /**
     * @param $attributeTableName
     * @throws \Zend_Db_Exception
     */
    public function createEavAttributeTable($attributeTableName)
    {
        $attributesTable = $this->setup->getConnection()
            ->newTable($this->setup->getTable($attributeTableName))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'ID'
            )
            ->addColumn(
                'entity_type_code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Entity Type Code'
            )
            ->addColumn(
                'attribute_code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Attribute Code'
            )
            ->addColumn(
                'frontend_input',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                [],
                'Frontend Input'
            )
            ->addColumn(
                'backend_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                8,
                ['nullable' => false],
                'Backend Type'
            )
            ->addColumn(
                'frontend_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Frontend Label'
            )
            ->addColumn(
                'source_model',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Source Model'
            )
            ->addColumn(
                'backend_model',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Backend Model'
            )
            ->addColumn(
                'scope',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Scope'
            )
            ->addColumn(
                'is_required',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Required'
            )
            ->addColumn(
                'visible_in_grid',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Visible In Grid'
            )
            ->addColumn(
                'has_option_codes',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Has Option Codes'
            )
            ->addColumn(
                'sort_order',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Sort Order'
            )
            ->addColumn(
                'is_user_defined',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is User Defined'
            )->addColumn(
                'default_value',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Default Value'
            )->addColumn(
                'is_unique',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Unique'
            )->addColumn(
                'frontend_class',
                Table::TYPE_TEXT,
                255,
                [],
                'Frontend Class'
            )->addColumn(
                'group_code',
                Table::TYPE_TEXT,
                255,
                [],
                'Attributes Group Code'
            )->addColumn(
                'input_validator',
                Table::TYPE_TEXT,
                255,
                [],
                'Attributes Input Validator'
            )->addColumn(
                'attribute_extra_params',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Attribute Extra Params'
            )->addColumn(
                'is_wysiwyg_enabled',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is WYSIWYG Enabled'
            )->addColumn(
                'note',
                Table::TYPE_TEXT,
                255,
                [],
                'Note'
            )
            ->addIndex(
                $this->setup->getIdxName(
                    $attributeTableName,
                    ['entity_type_code', 'attribute_code'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_type_code', 'attribute_code'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Alekseon EAV Attribute');
        $this->setup->getConnection()->createTable($attributesTable);
    }

    /**
     * @param $attributeTableName
     * @param $eavEntityTablesPrefix
     * @param null $entitiesToCreate
     * @param null $entityTableName
     * IMPORTANT: set entity table name for foreign key only if
     * eav entities tables are created for specific entity,
     * set null if entities are used for more entities
     * @param string $entityTableIdField
     * @throws \Zend_Db_Exception
     */
    public function createEavEntitiesTables(
        $attributeTableName,
        $eavEntityTablesPrefix,
        $entitiesToCreate = null,
        $entityTableName = null,
        $entityTableIdField = 'entity_id'
    ) {
        $entitiesConfig = $this->getEntitiesConfig();
        foreach ($entitiesConfig as $entityCode => $entityConfig) {
            if (is_array($entitiesToCreate) && !isset($entitiesToCreate[$entityCode])) {
                continue;
            }
            $eavEntityTableName = $eavEntityTablesPrefix . '_' . $entityCode;
            $eavEntityTable = $this->setup->getConnection()
                ->newTable($this->setup->getTable($eavEntityTableName))
                ->addColumn(
                    'value_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Value ID'
                )
                ->addColumn(
                    'attribute_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Attribute ID'
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Store ID'
                )
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Entity ID'
                )
                ->addColumn(
                    'value',
                    $entityConfig['value_column']['type'],
                    $entityConfig['value_column']['size'],
                    $entityConfig['value_column']['options'],
                    'Value'
                )
                ->addIndex(
                    $this->setup->getIdxName(
                        $eavEntityTableName,
                        ['entity_id', 'attribute_id', 'store_id'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['entity_id', 'attribute_id', 'store_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addIndex(
                    $this->setup->getIdxName($eavEntityTableName, ['attribute_id']),
                    ['attribute_id']
                )
                ->addIndex(
                    $this->setup->getIdxName($eavEntityTableName, ['store_id']),
                    ['store_id']
                )
                ->addForeignKey(
                    $this->setup->getFkName(
                        $eavEntityTableName,
                        'attribute_id',
                        $attributeTableName,
                        'id'
                    ),
                    'attribute_id',
                    $this->setup->getTable($attributeTableName),
                    'id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $this->setup->getFkName($eavEntityTableName, 'store_id', 'store', 'store_id'),
                    'store_id',
                    $this->setup->getTable('store'),
                    'store_id',
                    Table::ACTION_CASCADE
                )
                ->setComment($entityConfig['table_description']);

            if ($entityTableName) {
                $eavEntityTable->addForeignKey(
                    $this->setup->getFkName($eavEntityTableName, 'entity_id', $entityTableName, $entityTableIdField),
                    'entity_id',
                    $this->setup->getTable($entityTableName),
                    $entityTableIdField,
                    Table::ACTION_CASCADE
                );
            }

            $this->setup->getConnection()->createTable($eavEntityTable);
        }
    }

    /**
     * @param $attributeTableName
     * @param $optionsTableName
     * @throws \Zend_Db_Exception
     */
    public function createOptionTables($attributeTableName, $optionsTableName)
    {
        $optionsTable = $this->setup->getConnection()
            ->newTable($this->setup->getTable($optionsTableName))
            ->addColumn(
                'option_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Option Id'
            )
            ->addColumn(
                'option_code',
                Table::TYPE_TEXT,
                255,
                [],
                'Option Code'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Attribute Id'
            )
            ->addColumn(
                'sort_order',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Sort Order'
            )
            ->addIndex(
                $this->setup->getIdxName($optionsTableName, ['attribute_id']),
                ['attribute_id']
            )
            ->addForeignKey(
                $this->setup->getFkName($optionsTableName, 'attribute_id', $attributeTableName, 'id'),
                'attribute_id',
                $this->setup->getTable($attributeTableName),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Alekseon EAV Attribute Option');

        $this->setup->getConnection()->createTable($optionsTable);

        $optionValuesTableName = $optionsTableName . '_value';
        $optionValuesTable = $this->setup->getConnection()->newTable(
            $this->setup->getTable($optionValuesTableName)
        )->addColumn(
            'value_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Value Id'
        )->addColumn(
            'option_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Option Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store Id'
        )->addColumn(
            'value',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Value'
        )->addIndex(
            $this->setup->getIdxName($optionValuesTableName, ['option_id']),
            ['option_id']
        )->addIndex(
            $this->setup->getIdxName($optionValuesTableName, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $this->setup->getFkName($optionValuesTableName, 'option_id', $optionsTableName, 'option_id'),
            'option_id',
            $this->setup->getTable($optionsTableName),
            'option_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $this->setup->getFkName($optionValuesTableName, 'store_id', 'store', 'store_id'),
            'store_id',
            $this->setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Alekseon Eav Attribute Option Value'
        );
        $this->setup->getConnection()->createTable($optionValuesTable);
    }

    /**
     * @param $attributeTableName
     * @param $attributeFrontendLabelsTableName
     * @throws \Zend_Db_Exception
     */
    public function createFrontendLabelsTable($attributeTableName, $attributeFrontendLabelsTableName)
    {
        if ($this->setup->tableExists($this->setup->getTable($attributeFrontendLabelsTableName))) {
            return;
        }

        $attributeFrontendLabelsTable = $this->setup->getConnection()->newTable(
            $this->setup->getTable($attributeFrontendLabelsTableName)
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'attribute_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Attribute ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store Id'
        )->addColumn(
            'label',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Label'
        )->addIndex(
            $this->setup->getIdxName($attributeFrontendLabelsTableName, ['attribute_id']),
            ['attribute_id']
        )->addIndex(
            $this->setup->getIdxName($attributeFrontendLabelsTableName, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $this->setup->getFkName($attributeFrontendLabelsTableName, 'attribute_id', $attributeTableName, 'id'),
            'attribute_id',
            $this->setup->getTable($attributeTableName),
            'id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $this->setup->getFkName($attributeFrontendLabelsTableName, 'store_id', 'store', 'store_id'),
            'store_id',
            $this->setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Alekseon Eav Attribute Frontend Labels'
        );
        $this->setup->getConnection()->createTable($attributeFrontendLabelsTable);
    }

    /**
     * @param $attributeTableName
     * @param $optionsTableName
     */
    public function installOptionCodes($attributeTableName, $optionsTableName)
    {
        $this->setup->getConnection()->addColumn(
            $this->setup->getTable($attributeTableName),
            'has_option_codes',
            [
                'type' => Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Has Option Codes'
            ]
        );

        $this->setup->getConnection()->addColumn(
            $this->setup->getTable($optionsTableName),
            'option_code',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Option Code'
            ]
        );
    }
}
