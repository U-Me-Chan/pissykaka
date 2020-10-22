<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddNewColumnEstimateToTablePosts extends AbstractMigration
{
    public function up()
    {
        if ($this->table('posts') && !$this->table('posts')->hasColumn('estimate')) {
            $this->table('posts')->addColumn('estimate', 'integer', ['limit' => 10])->save();
            $this->execute('UPDATE posts SET estimate = 1');
        }
    }

    public function down()
    {
        if ($this->table('posts') && $this->table('posts')->hasColumn('estimate')) {
            $this->table('posts')->removeColumn('estimate')->save();
        }
    }
}
