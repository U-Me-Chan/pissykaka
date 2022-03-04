<?php

namespace PK\Posts;

use Medoo\Medoo;
use PK\Posts\Post\Post;
use PK\Boards\Board\Board;
use PK\Boards\BoardStorage;

class PostStorage
{
    public function __construct(
        private Medoo $db,
        private BoardStorage $board_storage
    ) {
    }

    public function find(int $limit = 20, int $offset = 0, array $tags = []): array
    {
        $conditions = [
            'LIMIT' => [$offset, $limit],
            'parent_id' => null,
            'ORDER' => ['updated_at' => 'DESC']
        ];

        $boards = [];

        foreach ($tags as $tag) {
            $board = $this->board_storage->findByTag($tag);

            $boards[$board->id] = $board->toArray();
        }

        $conditions['board_id'] = array_keys($boards);

        $post_datas = $this->db->select('posts', '*', $conditions);
        $count      = $this->db->count('posts', $conditions);

        if ($post_datas == null) {
            throw new \OutOfBoundsException();
        }

        $posts = [];

        foreach ($post_datas as $post_data) {
            $post_data['board_data'] = $boards[$post_data['board_id']];

            $post_data = array_merge($post_data, $this->getVerifyData($post_data['poster']));

            $replies = $this->db->select('posts', '*', ['parent_id' => $post_data['id'], 'LIMIT' => 3, 'ORDER' => ['id' => 'DESC']]);
            $replies_count = $this->db->count('posts', ['parent_id' => $post_data['id']]);

            if ($replies !== null) {
                foreach (array_reverse($replies) as $reply_data) {
                    $reply_data['board_data'] = $post_data['board_data'];

                    $reply_data = array_merge($reply_data, $this->getVerifyData($reply_data['poster']));

                    $post_data['replies'][] = Post::fromArray($reply_data);
                }

                $post_data['replies_count'] = $replies_count;
            }

            $posts[] = Post::fromArray($post_data);
        }

        return [$posts, $count];
    }

    public function findById(int $id): Post
    {
        $post_data = $this->db->get('posts', '*', ['id' => $id]);

        if ($post_data == null) {
            throw new \OutOfBoundsException();
        }

        /** @var Board */
        $board = $this->board_storage->findById($post_data['board_id']);

        $post_data['board_data'] = $board->toArray();

        $post_data = array_merge($post_data, $this->getVerifyData($post_data['poster']));

        $replies = $this->db->select('posts', '*', ['parent_id' => $post_data['id']]);
        $replies_count = $this->db->count('posts', ['parent_id' => $post_data['id']]);

        if ($replies !== null) {
            foreach ($replies as $reply_data) {
                $reply_data['board_data'] = $post_data['board_data'];

                $reply_data = array_merge($reply_data, $this->getVerifyData($reply_data['poster']));

                $post_data['replies'][] = Post::fromArray($reply_data);
            }

            $post_data['replies_count'] = $replies_count;
        }

        return Post::fromArray($post_data);
    }

    public function save(Post $post): int
    {
        $id = $post->id;

        $post_data = $post->toArray();
        unset($post_data['id']);

        if ($id == 0) {
            $this->db->insert('posts', $post_data);

            return $this->db->id();
        }

        $this->db->update('posts', $post_data, ['id' => $id]);
    }

    public function delete(int $id): bool
    {
        /** @var PDOStatement */
        $pdo = $this->db->delete('posts', ['id' => $id]);

        if ($pdo->rowCount() == 1) {
            return true;
        }

        throw new \OutOfBoundsException();
    }

    private function getVerifyData(string $poster): array
    {
        $hash = hash('sha512', $poster);

        $name = $this->db->get('passports', 'name', ['hash' => $hash]);

        if ($name == null) {
            return [
                'poster'    => $poster,
                'is_verify' => false
            ];
        }

        return [
            'poster'    => $name,
            'is_verify' => true
        ];
    }
}
