<?php

namespace PK\Controllers;

use Medoo\Medoo;
use PK\Http\Request;
use PK\Http\Response;
use PK\Database\BoardRepository;
use PK\Exceptions\Board\BoardNotFound;

class BoardsFetcher
{
    /** @var BoardRepository */
    private $repository;

    /** @var Medoo */
    private $db;

    public function __construct(BoardRepository $repository, Medoo $db)
    {
        $this->repository = $repository;
        $this->db = $db;
    }

    public function __invoke(Request $req)
    {
        try {
            $boards = $this->repository->fetch();
        } catch (BoardNotFound $e) {
            return (new Response([], 404))->setException(new BoardNotFound('Нет досок, создайте хотя бы одну'));
        }

        $results = [];

        foreach ($boards as $board) {
            $results['boards'][] = $board->toArray();
        }

        $results['posts'] = $this->db->select(
            'posts',
            [
                '[>]boards' => [
                    'board_id' => 'id'
                ]
            ],
            [
                'posts.id',
                'posts.poster',
                'posts.subject',
                'posts.message',
                'posts.timestamp',
                'posts.parent_id',
                'boards.tag'
            ],
            [
                'AND' => ['boards.tag[!]' => 'test'],
                'LIMIT' => 20,
                'ORDER' => ['posts.timestamp' => 'DESC']
            ]
        );

        return new Response($results, 200);
    }
}
