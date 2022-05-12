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

        $exclude_tags = $req->getParams('exclude_tags') ? $req->getParams('exclude_tags') : ['test', 'fap'];
        $limit        = $req->getParams('limit') ? $req->getParams('limit') : 20;
        $offset       = $req->getParams('offset') ? $req->getParams('offset') : 0;

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
                'AND' => ['boards.tag[!]' => $exclude_tags],
                'LIMIT' => [$offset, $limit],
                'ORDER' => ['posts.timestamp' => 'DESC']
            ]
        );

        return new Response($results, 200);
    }
}
