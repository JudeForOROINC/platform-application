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
        $table->addColumn('parent_issue_id', 'integer', ['notnull' => false]);
        $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('summary', 'string', ['length' => 255]);
        $table->addColumn('code', 'string', ['length' => 10]);
        $table->addColumn('description', 'text', []);
        $table->addColumn('issue_type', 'string', ['length' => 30]);
        $table->addColumn('created', 'datetime', []);
        $table->addColumn('updated', 'datetime', []);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['code'], 'UNIQ_CF2A577B77153098');
        $table->addIndex(['user_owner_id'], 'IDX_CF2A577B9EB185F9', []);
//        $table->addIndex(['parent_issue_id'], 'IDX_CF2A577BC1B7095D', []);
    }

    /**
     * Add magecore_testtaskoro_issue foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMagecoreTesttaskoroIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('magecore_testtaskoro_issue');
//        $table->addForeignKeyConstraint(
//            $schema->getTable('magecore_testtaskoro_issue'),
//            ['parent_issue_id'],
//            ['id'],
//            ['onDelete' => 'CASCADE', 'onUpdate' => null]
//        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_owner_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }
}
