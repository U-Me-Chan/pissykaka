<?php

namespace PK\Controllers;

use Medoo\Medoo;
use PK\Database\PostRepository;
use PK\Http\Request;
use PK\Http\Response;

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
        return new Response([], 403);

        $id = $vars['id'];

        $result = $this->repository->delete($id);

        if (!$result) {
            return (new Response([], 404))->setException(new PostNotFound('Нельзя удалить несуществующий пост'));
        }

        return new Response([], 204);
    }
}
