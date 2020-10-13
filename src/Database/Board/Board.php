<?php

namespace PK\Database\Board;

class Board
{
    private $id;
    private $tag;
    private $name;

    public function __construct(int $id, string $tag, string $name)
    {
        $this->id   = $id;
        $this->tag  = $tag;
        $this->name = $name;
    }

    public static function fromState(array $state): self
    {
        return new self(
            $state['id'],
            $state['tag'],
            $state['name']
        );
    }

    public function getId(): int
    {
        if (!$this->id) {
            throw new \RuntimeException("Эта доска ещё не была создана");
        }

        return $this->id;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tag' => $this->tag,
            'name' => $this->name
        ];
    }
}
