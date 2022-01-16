<?php

namespace PK\Controllers;

use PK\Database\PostRepository;
use PK\Database\BoardRepository;
use PK\Http\Request;
use PK\Http\Response;
use PK\Exceptions\Board\BoardNotFound;
use PK\Exceptions\Post\PostNotFound;

class PostBoardFetcher
{
    private $board_repo;
    private $post_repo;

    public function __construct(BoardRepository $board_repo, PostRepository $post_repo)
    {
        $this->board_repo = $board_repo;
        $this->post_repo = $post_repo;
    }

    public function __invoke(Request $req, array $vars): Response
    {
        $board_name = $vars['tag'];

        try {
            $board = $this->board_repo->findByTag($board_name);
        } catch (BoardNotFound $e) {
            return (new Response([], 404))->setException($e);
        }

        $results['board_data'] = $board->toArray();
        $results['board_data']['threads'] = [];

        $limit  = $req->getParams('limit') ? $req->getParams('limit') : 20;
        $offset = $req->getParams('offset') ? $req->getParams('offset') : 0;

        try {
            $posts = $this->post_repo->findByBoardId($board->getId(), $limit, $offset);
        } catch (PostNotFound $e) {
            return (new Response($results, 200))->setException($e);
        }

        foreach ($posts as $post) {
            $results['board_data']['threads'][] = $post->toArray();
        }

        return new Response($results, 200);
    }
}
