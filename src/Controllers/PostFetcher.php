<?php

namespace PK\Controllers;

use PK\Database\PostRepository;
use PK\Http\Request;
use PK\Http\Response;
use PK\Exceptions\Post\PostNotFound;

class PostFetcher
{
    private $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $req, array $vars)
    {
        $id = $vars['id'];

        try {
            $post = $this->repository->findById($id);
        } catch (PostNotFound $e) {
            return (new Response([], 404))->setException(new PostNotFound());
        }

        $results['thread_data'] = $post->toArray();
        $results['thread_data']['replies_count'] = $this->repository->getRepliesCount($id);

        try {
            $replies = $this->repository->fetchReplies($id);

            foreach ($replies as $reply) {
                $results['thread_data']['replies'][] = $reply->toArray();
            }
        } catch (PostNotFound $e) {
            $results['thread_data']['replies'] = [];
        }

        return new Response($results, 200);
    }
}
