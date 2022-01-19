<?php

namespace PK\Controllers;

use Medoo\Medoo;
use PK\Database\PostRepository;
use PK\Http\Request;
use PK\Http\Response;
use PK\Exceptions\Post\PostNotFound;
use PK\Database\Post\Post;

class PostDeleter
{
    /** @var PostRepository */
    private $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $req, array $vars): Response
    {
        if (!$req->getParams('password')) {
            return (new Response([], 400))->setException(new \InvalidArgumentException('Укажите пароль для удаления поста'));
        }

        try {
            /** @var Post */
            $post = $this->repository->findById($vars['id']);
        } catch (PostNotFound $e) {
            return (new Response([], 404))->setException($e);
        }

        if (hash_equals($req->getParams('password'), $post->getPassword())) {
            $this->repository->delete($vars['id']);

            return new Response([], 204);
        }

        return new Response([], 401);
    }
}
