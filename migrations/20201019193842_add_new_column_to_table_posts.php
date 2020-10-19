<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddNewColumnToTablePosts extends AbstractMigration
{
    public function up()
    {
        if ($this->table('posts') && !$this->table('posts')->hasColumn('updated_at')) {
            $this->table('posts')->addColumn('updated_at', 'integer', ['limit' => 10])->save();
        }
    }

    public function down()
    {
        if ($this->table('posts') && $this->table('posts')->hasColumn('update_at')) {
            $this->table('posts')->removeColumn('updated_at')->save();
        }
    }
}
