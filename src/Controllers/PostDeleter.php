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

    public function __invoke(Request $req): Response
    {
        if (!$req->getParams('id')) {
            return (new Response([], 400))->setException(new \InvalidArgumentException('Не задан идентификатор удаляемого поста'));
        }

        $result = $this->repository->delete($req->getParams('id'));

        if (!$result) {
            return (new Response([], 404))->setException(new PostNotFound('Нельзя удалить несуществующий пост'));
        }

        return new Response([], 204);
    }
}
