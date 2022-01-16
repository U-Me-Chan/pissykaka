<?php

namespace PK\Database\Post;

class Post
{
    private const COEFFICIENT = 1602370000;
    private const BOARD_ID = 1;
    private const NAME = 'Anonymous';

    private $id;
    private $poster;
    private $subject;
    private $message;
    private $timestamp;
    private $board_id;
    private $parent_id;
    private $updated_at;
    private $estimate;

    public function __construct(int $id, string $poster, string $subject, string $message, int $timestamp, int $board_id, $parent_id, int $updated_at, int $estimate = 0)
    {
        $this->id         = $id;
        $this->poster     = $poster;
        $this->subject    = $subject;
        $this->message    = $message;
        $this->timestamp  = $timestamp;
        $this->board_id   = $board_id;
        $this->parent_id  = $parent_id;
        $this->updated_at = $updated_at;
        $this->estimate   = $estimate;
    }

    public static function fromState(array $state): self
    {
        return new self(
            $state['id'],
            $state['poster'],
            $state['subject'],
            $state['message'],
            $state['timestamp'],
            $state['board_id'],
            $state['parent_id'],
            $state['updated_at'],
            $state['estimate']
        );
    }

    public function getId()
    {
        if (!$this->id) {
            throw new \RuntimeException('Пост ещё не был создан');
        }

        return $this->id;
    }

    public function getPoster(): string
    {
        if (!$this->poster) {
            return 'Anonymous';
        }

        return $this->poster;
    }

    public function getSubject(): string
    {
        if (!$this->subject) {
            return '';
        }

        return $this->subject;
    }

    public function getMessage(): string
    {
        if (!$this->message) {
            return '';
        }

        return $this->message;
    }

    public function getTimestamp(): int
    {
        if (!$this->timestamp) {
            return time();
        }

        return $this->timestamp;
    }

    public function getBoardId(): int
    {
        return $this->board_id;
    }

    public function getParentId()
    {
        return $this->parent_id;
    }

    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

    public function getEstimate(): int
    {
        if ($this->estimate !== 0) {
            return $this->estimate;
        }

        $x = $this->getTimestamp() / self::COEFFICIENT;

        if ($this->getPoster() !== self::NAME) {
            $x = $x / 2;
        }

        if ($this->getParentId() == null) {
            $x = $x + 1;
        }

        if ($this->getBoardId() !== self::BOARD_ID) {
            $x = $x + 2;
        }

        $len_message = sprintf('0.%d', strlen($this->getMessage()));

        $x = $len_message == 0 ? 0 : $x / $len_message;

        return (int) $x;
    }

    public function setEstimate(int $estimate): void
    {
        $this->estimate = $estimate;
    }

    public function setUpdatedAt(int $timestamp): void
    {
        $this->updated_at = $timestamp;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'poster' => $this->poster,
            'subject' => $this->subject,
            'message' => $this->message,
            'timestamp' => $this->timestamp,
            'board_id' => $this->board_id,
            'parent_id' => $this->parent_id,
            'updated_at' => $this->updated_at,
            'estimate'   => $this->estimate
        ];
    }
}
