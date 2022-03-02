<?php

namespace PK\Posts\Controllers;

use PK\Http\Request;
use PK\Http\Response;
use PK\Posts\PostStorage;

final class DeletePost
{
    public function __construct(
        private PostStorage $storage,
        private string $key
    ) {
    }

    public function __invoke(Request $req, array $vars): Response
    {
        if ($req->getHeaders('HTTP_KEY') == null) {
            return new Response([], 401);
        }

        if ($req->getHeaders('HTTP_KEY') !== $this->key) {
            return new Response([], 401);
        }

        $id = $vars['id'];

        try {
            $res = $this->storage->delete($id);

            return new Response(['result' => true], 200);
        } catch (\OutOfBoundsException $e) {
            return new Response([], 404);
        }
    }
}
