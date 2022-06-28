<?php

namespace PK\Boards;

use Medoo\Medoo;
use PK\Boards\Board\Board;

class BoardStorage
{
    public function __construct(
        private Medoo $db
    ) {
    }

    public function find(array $exclude_tags = []): array
    {
        $conditions = [
            'ORDER' => ['tag' => 'ASC']
        ];

        if (!empty($exclude_tags)) {
            $conditions['tag[!]'] = $exclude_tags;
        }

        $board_datas = $this->db->select('boards', '*', $conditions);

        if (!$board_datas) {
            return [];
        }

        $boards = [];

        foreach ($board_datas as $board_data) {
            list ($threads_count, $new_posts_count) = $this->getCounters($board_data['id']);
            $board_data['threads_count'] = $threads_count;
            $board_data['new_posts_count'] = $new_posts_count;

            $boards[] = Board::fromArray($board_data);
        }

        return $boards;
    }

    private function getCounters(int $id): array
    {
        $threads_count   = $this->db->count('posts', ['board_id' => $id, 'parent_id' => null]);
        $new_posts_count = $this->db->count('posts', ['board_id' => $id, 'timestamp[>]' => time() - (60 * 60 * 24)]);

        return [$threads_count, $new_posts_count];
    }

    public function findByTag(string $tag): Board
    {
        $board_data = $this->db->get('boards', '*', ['tag' => $tag]);

        if ($board_data == null) {
            throw new \OutOfBoundsException();
        }

        list ($threads_count, $new_posts_count) = $this->getCounters($board_data['id']);
        $board_data['threads_count'] = $threads_count;
        $board_data['new_posts_count'] = $new_posts_count;

        return Board::fromArray($board_data);
    }

    public function findById(int $id): Board
    {
        $board_data = $this->db->get('boards', '*', ['id' => $id]);

        if ($board_data == null) {
            throw new \OutOfBoundsException();
        }

        list ($threads_count, $new_posts_count) = $this->getCounters($board_data['id']);
        $board_data['threads_count'] = $threads_count;
        $board_data['new_posts_count'] = $new_posts_count;

        return Board::fromArray($board_data);
    }

    public function save(Board $board): int
    {
        $id = $board->id;

        $board_data = $board->toArray();

        unset($board_data['id']);

        if ($id == 0) {
            $this->db->insert('boards', $board_data);

            return $this->db->id();
        }

        $this->db->update('boards', $board_data, ['id' => $id]);

        return $id;
    }
}
