<?php

namespace PK\Boards\Board;

class Board implements \JsonSerializable
{
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    public static function fromArray(array $state): self
    {
        return new self(
            $state['id'],
            $state['tag'],
            $state['name'],
            $state['threads_count'],
            $state['new_posts_count']
        );
    }

    private function __construct(
        public int $id,
        public string $tag,
        public string $name,
        public int $threads_count,
        public int $new_posts_count
    ) {
    }
}
