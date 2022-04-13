<?php

namespace PK\Posts\Controllers;

use PK\Http\Request;
use PK\Http\Response;
use PK\Posts\PostStorage;
use PK\Posts\Post\Post;

final class CreateReply
{
    public function __construct(
        private PostStorage $storage
    ) {
    }

    public function __invoke(Request $req, array $vars): Response
    {
        $parent_id = $vars['id'];

        if ($req->getParams('message') == null) {
            return new Response([], 400);
        }

        try {
            $thread = $this->storage->findById($parent_id);
        } catch (\OutOfBoundsException $e) {
            return new Response([], 404);
        }

        $post = Post::draft($thread->board, $parent_id, $req->getParams('message'));

        if ($req->getParams('poster')) {
            $post->poster = $req->getParams('poster');
        }

        if ($req->getParams('subject')) {
            $post->subject = $req->getParams('subject');
        }

        $id = $this->storage->save($post);

        if ($thread->replies_count < 500 && !$req->getParams('sage')) {
            $thread->updated_at = time();

            $this->storage->save($thread);
        }

        return new Response(['post_id' => $id, 'password' => $post->password], 201);
    }
}
