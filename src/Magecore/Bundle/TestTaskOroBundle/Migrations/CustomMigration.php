<?php
/**
 * Created by PhpStorm.
 * User: jude
 * Date: 23.07.15
 * Time: 11:03
 */
namespace Magecore\Bundle\TestTaskOroBundle\Migration;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class CustomMigration implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        // ...
    }
}