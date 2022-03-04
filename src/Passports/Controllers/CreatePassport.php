<?php

namespace PK\Passports\Controllers;

use Medoo\Medoo;
use PK\Http\Request;
use PK\Http\Response;

final class CreatePassport
{
    public function __construct(
        private Medoo $db
    ) {
    }

    public function __invoke(Request $req): Response
    {
        if ($req->getParams('name') == null) {
            return new Response([], 400);
        }

        if (empty($req->getParams('name'))) {
            return new Response([], 400);
        }

        if ($req->getParams('key') == null) {
            return new Response([], 400);
        }

        if (empty($req->getParams('key'))) {
            return new Response([], 400);
        }

        try {
            $this->db->insert('passports', [
                'name' => $req->getParams('name'),
                'hash'  => hash('sha512', $req->getParams('key'))
            ]);
        } catch (\PDOException $e) {
            return (new Response([], 409))->setException(new \Exception("Такой ключ или имя уже используется, выберите иное"));
        }

        return new Response([], 201);
    }
}
