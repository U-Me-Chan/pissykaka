<?php

namespace PK\Database\Post;

class Post
{
    private $id;
    private $poster;
    private $subject;
    private $message;
    private $timestamp;
    private $board_id;
    private $parent_id;
    private $updated_at;

    public function __construct(int $id, string $poster, string $subject, string $message, int $timestamp, int $board_id, $parent_id, int $updated_at)
    {
        $this->id = $id;
        $this->poster = $poster;
        $this->subject = $subject;
        $this->message = $message;
        $this->timestamp = $timestamp;
        $this->board_id = $board_id;
        $this->parent_id = $parent_id;
        $this->updated_at = $updated_at;
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
            $state['updated_at']
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
            'updated_at' => $this->updated_at
        ];
    }
}
