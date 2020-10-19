<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddValuesToColumnUpdatedAtInTablePosts extends AbstractMigration
{
    public function up()
    {
        if ($this->table('posts') && $this->table('posts')->hasColumn('updated_at')) {
            $this->execute(sprintf('UPDATE posts SET updated_at = %d WHERE parent_id IS NULL', time()));
        }
    }

    public function down()
    {
        if ($this->table('posts') && $this->table('posts')->hasColumn('updated_at')) {
            $this->execute('UPDATE posts SET updated_at = 0 WHERE parent_id IS NULL');
        }
    }
}
