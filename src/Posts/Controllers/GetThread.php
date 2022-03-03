<?php

namespace PK\Posts\Controllers;

use PK\Http\Request;
use PK\Http\Response;
use PK\Posts\PostStorage;
use PK\Posts\Post\Post;

final class GetThread
{
    public function __construct(
        private PostStorage $storage
    ) {
    }

    public function __invoke(Request $req, array $vars): Response
    {
        $id = $vars['id'];

        try {
            $post = $this->storage->findById($id);
        } catch (\OutOfBoundsException $e) {
            return new Response([], 404);
        }

        return new Response(['thread_data' => $post]);
    }
}
