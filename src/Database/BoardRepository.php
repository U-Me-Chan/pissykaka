<?php

namespace PK\Database;

use PK\Database\ARepository;
use PK\Database\Board\Board;
use PK\Exceptions\Board\BoardNotFound;

class BoardRepository extends ARepository
{
    private const TABLE = 'boards';

    private const ID = 'id';
    private const TAG = 'tag';
    private const NAME = 'name';

    private const EXCLUDED_TAGS = ['test'];

    public function fetch(): array
    {
        $boards_data = $this->db->select(self::TABLE, $this->getFields(), ['AND' => ['tag[!]' => self::EXCLUDED_TAGS], 'ORDER' => [self::TAG => 'ASC']]);

        if (!$boards_data) {
            throw new BoardNotFound('Не найдено ни одной доски');
        }

        $boards = [];

        foreach ($boards_data as $board_data) {
            $boards[] = Board::fromState($board_data);
        }

        return $boards;
    }

    public function findByTag(string $tag): Board
    {
        $board_data = $this->db->get(self::TABLE, $this->getFields(), ['tag' => $tag]);

        if (!$board_data) {
            throw new BoardNotFound('Доски с таким тегом не найдено');
        }

        return Board::fromState($board_data);
    }

    public function findById(int $id): Board
    {
        $board_data = $this->db->get(self::TABLE, $this->getFields(), ['id' => $id]);

        if (!$board_data) {
            throw new BoardNotFound('Доски с таким идентификатором не найдено');
        }

        return Board::fromState($board_data);
    }

    public function save(Board $board): int
    {
        $this->db->insert(self::TABLE, [
            'tag' => $board->getTag(),
            'name' => $board->getName()
        ]);

        return $this->db->id();
    }

    private function getFields(): array
    {
        return [
            self::ID,
            self::TAG,
            self::NAME
        ];
    }
}
