<?php

namespace PK\Database;

use Medoo\Medoo;

abstract class ARepository
{
    /** @var Medoo */
    protected $db;

    public function __construct(Medoo $db)
    {
        $this->db = $db;
    }
}
