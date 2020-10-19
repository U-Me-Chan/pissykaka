<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RenameColumnNameToTagInTableBoardsAndAddColumnName extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('boards');

        if ($table && !$table->hasColumn('tag')) {
            $table->renameColumn('name', 'tag')
                  ->addColumn('name', 'string', ['limit' => 100])
                  ->save();
        }
    }

    public function down()
    {
        $table = $this->table('boards');

        if ($table && $table->hasColumn('tag')) {
            $table->removeColumn('name')->save();
            $table->renameColumn('tag', 'name')->save();
        }
    }
}
