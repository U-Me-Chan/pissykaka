<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class V20220303230427 extends AbstractMigration
{
    public function change(): void
    {
        $this->table('passports', ['id' => false, 'primary_key' => ['name', 'hash']])
             ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
             ->addColumn('hash', 'string', ['limit' => 255, 'null' => false])
             ->addIndex(['hash'], ['unique' => true])
             ->addIndex(['name'], ['unique' => true])
             ->create();
    }
}
