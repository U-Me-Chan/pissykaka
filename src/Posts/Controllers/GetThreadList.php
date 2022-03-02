<?php

namespace PK\Posts\Controllers;

use PK\Http\Request;
use PK\Http\Response;
use PK\Posts\PostStorage;

final class GetThreadList
{
    public function __construct(
        private PostStorage $storage
    ) {
    }

    public function __invoke(Request $req, array $vars): Response
    {
        $limit  = $req->getParams('limit') ? $req->getParams('limit') : 20;
        $offset = $req->getParams('offset') ? $req->getParams('offset') : 0;

        $tags = explode('+', $vars['tags']);

        try {
            list($posts, $count) = $this->storage->find($limit, $offset, $tags);
        } catch (\OutOfBoundsException $e) {
            return new Response([], 400);
        }

        return new Response(['count' => $count, 'posts' => $posts]);
    }
}
