<?php

namespace Magecore\Bundle\TestTaskOroBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class MagecoreTestTaskOroBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createMagecoreTesttaskoroIssueTable($schema);
        $this->createMagecoreTesttaskoroPriorityTable($schema);

        /** Foreign keys generation **/
        $this->addMagecoreTesttaskoroIssueForeignKeys($schema);
    }

    /**
     * Create magecore_testtaskoro_issue table
     *
     * @param Schema $schema
     */
    protected function createMagecoreTesttaskoroIssueTable(Schema $schema)
    {
        $table = $schema->createTable('magecore_testtaskoro_issue');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('priority_name', 'string', ['notnull' => false, 'length' => 16]);
        $table->addColumn('reporter_id', 'integer', ['notnull' => false]);
        $table->addColumn('assigned_to_id', 'integer', ['notnull' => false]);
        $table->addColumn('summary', 'string', ['length' => 255]);
        $table->addColumn('code', 'string', ['length' => 14]);
        $table->addColumn('description', 'text', []);
        $table->addColumn('issue_type', 'string', ['length' => 30]);
        $table->addColumn('createdAt', 'datetime', []);
        $table->addColumn('updatedAt', 'datetime', []);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['code'], 'UNIQ_CF2A577B77153098');
        $table->addIndex(['reporter_id'], 'IDX_CF2A577BE1CFE6F5', []);
        $table->addIndex(['assigned_to_id'], 'IDX_CF2A577BF4BD7827', []);
        $table->addIndex(['priority_name'], 'IDX_CF2A577B965BD3DF', []);
    }

    /**
     * Create magecore_testtaskoro_priority table
     *
     * @param Schema $schema
     */
    protected function createMagecoreTesttaskoroPriorityTable(Schema $schema)
    {
        $table = $schema->createTable('magecore_testtaskoro_priority');
        $table->addColumn('name', 'string', ['length' => 16]);
        $table->addColumn('order', 'integer', []);
        $table->addColumn('label', 'string', ['length' => 255]);
        $table->setPrimaryKey(['name']);
    }

    /**
     * Add magecore_testtaskoro_issue foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMagecoreTesttaskoroIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('magecore_testtaskoro_issue');
        $table->addForeignKeyConstraint(
            $schema->getTable('magecore_testtaskoro_priority'),
            ['priority_name'],
            ['name'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['reporter_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['assigned_to_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }
}
