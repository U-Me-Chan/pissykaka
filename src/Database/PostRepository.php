<?php

namespace PK\Database;

use Medoo\Medoo;
use PK\Database\Post\Post;
use PK\Exceptions\Post\PostNotFound;

class PostRepository
{
    private const TABLE = 'posts';

    private const ID        = 'id';
    private const POSTER    = 'poster';
    private const SUBJECT   = 'subject';
    private const MESSAGE   = 'message';
    private const TIMESTAMP = 'timestamp';
    private const BOARD_ID  = 'board_id';
    private const PARENT_ID = 'parent_id';
    /** @var Medoo */
    private $db;

    public function __construct(Medoo $db)
    {
        $this->db = $db;
    }

    public function fetchReplies(int $id): array
    {
        $replies_data = $this->db->select(self::TABLE, $this->getFields(), ['parent_id' => $id]);

        if (!$replies_data) {
            throw new PostNotFound('Ответы не найдены');
        }

        $replies = [];

        foreach ($replies_data as $reply_data) {
            $replies[] = Post::fromState($reply_data);
        }

        return $replies;
    }

    public function findByBoardId(int $board_id): array
    {
        $posts_data = $this->db->select(self::TABLE, $this->getFields(), ['AND' => ['board_id' => $board_id, 'parent_id' => null]]);

        if (!$posts_data) {
            throw new PostNotFound('Треды не найдены');
        }

        $posts = [];

        foreach ($posts_data as $post_data) {
            $posts[] = Post::fromState($post_data);
        }

        return $posts;
    }

    public function findById(int $id): Post
    {
        $post_data = $this->db->get(self::TABLE, $this->getFields(), ['id' => $id]);

        if (!$post_data) {
            throw new PostNotFound('Пост не найден');
        }

        return Post::fromState($post_data);
    }

    public function save(Post $post): int
    {
        $this->db->insert(self::TABLE, [
            'poster' => $post->getPoster(),
            'subject' => $post->getSubject(),
            'message' => $post->getMessage(),
            'timestamp' => $post->getTimestamp(),
            'board_id' => $post->getBoardId(),
            'parent_id' => $post->getParentId()
        ]);

        return $this->db->id();
    }

    public function delete(int $id): bool
    {
        return $this->db->delete(self::TABLE, ['AND' => ['id' => $id]]);
    }

    public function getNewId(): int
    {
        return $this->db->max(self::TABLE, 'id');
    }

    private function getFields(): array
    {
        return [
            self::ID,
            self::POSTER,
            self::SUBJECT,
            self::MESSAGE,
            self::TIMESTAMP,
            self::BOARD_ID,
            self::PARENT_ID
        ];
    }
}
