<?php

namespace PK\Controllers;

use PK\Http\Request;
use PK\Http\Response;
use PK\Database\BoardRepository;
use PK\Exceptions\Board\BoardNotFound;

class BoardsFetcher
{
    /** @var BoardRepository */
    private $repository;

    public function __construct(BoardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $req)
    {
        $boards = $this->repository->fetch();

        if (!$boards) {
            return (new Response([], 404))->setException(new BoardNotFound());
        }

        $results = [];

        foreach ($boards as $board) {
            $results[] = $board->toArray();
        }

        return new Response($results, 200);
    }
}
