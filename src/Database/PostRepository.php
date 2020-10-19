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
    private const UPDATED_AT = 'updated_at';

    /** @var Medoo */
    private $db;

    public function __construct(Medoo $db)
    {
        $this->db = $db;
    }

    public function fetchReplies(int $id): array
    {
        $replies_data = $this->db->select(self::TABLE, $this->getFields(), [self::PARENT_ID => $id]);

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
        $posts_data = $this->db->select(
            self::TABLE,
            $this->getFields(),
            [
                'AND' => [
                    self::BOARD_ID  => $board_id,
                    self::PARENT_ID => null
                ],
                'ORDER' => [
                    self::UPDATED_AT => 'DESC'
                ]
            ]
        );

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
        $post_data = $this->db->get(self::TABLE, $this->getFields(), [self::ID => $id]);

        if (!$post_data) {
            throw new PostNotFound('Пост не найден');
        }

        return Post::fromState($post_data);
    }

    public function save(Post $post): int
    {
        $this->db->insert(self::TABLE, [
            self::POSTER => $post->getPoster(),
            self::SUBJECT => $post->getSubject(),
            self::MESSAGE => $post->getMessage(),
            self::TIMESTAMP => $post->getTimestamp(),
            self::BOARD_ID => $post->getBoardId(),
            self::PARENT_ID => $post->getParentId(),
            self::UPDATED_AT => $post->getUpdatedAt()
        ]);

        return $this->db->id();
    }

    public function update(Post $post): bool
    {
        $this->db->update(self::TABLE, [
            self::POSTER => $post->getPoster(),
            self::SUBJECT => $post->getSubject(),
            self::MESSAGE => $post->getMessage(),
            self::TIMESTAMP => $post->getTimestamp(),
            self::BOARD_ID => $post->getBoardId(),
            self::PARENT_ID => $post->getParentId(),
            self::UPDATED_AT => $post->getUpdatedAt()
        ], [self::ID => $post->getId()]);

        return true;
    }

    public function delete(int $id): bool
    {
        return $this->db->delete(self::TABLE, ['AND' => [self::ID => $id]]);
    }

    public function getNewId(): int
    {
        return $this->db->max(self::TABLE, self::ID);
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
            self::PARENT_ID,
            self::UPDATED_AT
        ];
    }
}
