<?php

namespace PK\Controllers;

use PK\Database\PostRepository;
use PK\Database\BoardRepository;
use PK\Database\Post\Post;
use PK\Database\Board\Board;
use PK\Http\Request;
use PK\Http\Response;
use PK\Exceptions\Post\PostNotFound;
use PK\Exceptions\Board\BoardNotFound;

class PostCreator
{
    private $post_repository;
    private $board_repository;

    public function __construct(PostRepository $post_repository, BoardRepository $board_repository)
    {
        $this->post_repository = $post_repository;
        $this->board_repository = $board_repository;
    }

    public function __invoke(Request $req): Response
    {
        if ($req->getParams('parent_id')) {
            try {
                /** @var Post */
                $parent_post = $this->post_repository->findById($req->getParams('id'));
            } catch (PostNotFound $e) {
                return (new Response([], 400))->setException(new PostNotFound('Попытка ответа на несуществующий пост'));
            }

            $post = new Post(
                $this->post_repository->getNewId(),
                $req->getParams('poster') ? $req->getParams('poster') : 'Anonymous',
                $req->getParams('subject') ? $req->getParams('subject') : '',
                $req->getParams('message') ? $req->getParams('message') : '',
                $parent_post->getBoardId(),
                $req->getParams('parent_id')
            );

            $new_post_id = $this->post_repository->save($post);

            return new Response(['post_id' => $new_post_id], 201);
        }

        if (!$req->getParams('tag')) {
            return (new Response([], 400))->setException(new \InvalidArgumentException('Не задано тег доски, на которой должен быть создан пост'));
        }

        $board = $this->board_repository->findByTag($req->getParams('tag'));

        if (!$board) {
            return (new Response([], 400))->setException(new BoardNotFound('Не найдена доска с таким тегом'));
        }

        $post = new Post(
            $this->post_repository->getNewId(),
            $req->getParams('poster') ? $req->getParams('poster') : 'Anonymous',
            $req->getParams('subject') ? $req->getParams('subject') : '',
            $req->getParams('message') ? $req->getParams('message') : '',
            $board->getId(),
            null
        );

        $new_post_id = $this->post_repository->save($post);

        return new Response(['post_id' => $new_post_id], 201);
    }
}