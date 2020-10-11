<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitPostsTable extends AbstractMigration
{
    public function up()
    {
        if (!$this->hasTable('posts')) {
            $this->table('posts')
                 ->addColumn('poster', 'string', ['limit' => 100])
                 ->addColumn('subject', 'string', ['limit' => 100])
                 ->addColumn('message', 'string', ['limit' => 10000])
                 ->addColumn('timestamp', 'integer', ['limit' => 10])
                 ->addColumn('board_id', 'integer')
                 ->addColumn('parent_id', 'integer', ['null' => true])
                 ->addForeignKey('board_id', 'boards', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                 ->save();
        }
    }

    public function down()
    {
        if ($this->hasTable('posts')) {
            $this->table('posts')->drop()->save();
        }
    }
}
