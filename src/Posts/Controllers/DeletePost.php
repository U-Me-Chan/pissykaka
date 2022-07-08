<?php

namespace PK\Posts\Controllers;

use PK\Http\Request;
use PK\Http\Response;
use PK\Posts\PostStorage;
use PK\Posts\Post\Post;

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
            return (new Response([], 401))->setException(new \InvalidArgumentException("Не задан мастер-ключ"));
        }

        if ($req->getHeaders('HTTP_KEY') !== $this->key) {
            return (new Response([], 401))->setException(new \InvalidArgumentException("Неверный мастер-ключ"));
        }

        $reason = $req->getHeaders('HTTP_REASON') ? $req->getHeaders('HTTP_REASON') : 'Не указано';

        $id = $vars['id'];

        try {
            /** @var Post */
            $post = $this->storage->findById($id);

            $post->subject = '⬛⬛⬛⬛⬛⬛⬛⬛⬛';
            $post->poster = '⬛⬛⬛⬛⬛⬛⬛⬛⬛';
            $post->message = '⬛⬛⬛⬛⬛⬛⬛⬛⬛';

            $post->message = <<<EOT
{$post->message}

Данные удалены по причине: {$reason}
EOT;

            $this->storage->save($post);

            return new Response([], 204);
        } catch (\OutOfBoundsException $e) {
            return (new Response([], 404))->setException(new \OutOfBoundsException("Нет такого поста"));
        }
    }
}
