<?php

namespace PK\Database\Post;

use PK\Database\Post\Media;

class Post
{
    private const COEFFICIENT = 1602370000;
    private const BOARD_ID = 1;
    private const NAME = 'Anonymous';

    public function __construct(
        private int $id,
        private string $poster,
        private string $subject,
        private string $message,
        private int $timestamp,
        private int $board_id,
        private int|null $parent_id,
        private int $updated_at,
        private int $estimate = 0,
        private string $password = ''
    ) {
        $this->password = empty($this->password) ? hash('sha256', bin2hex(random_bytes(5))) : $this->password;
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
            $state['estimate'],
            $state['password']
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
            'id'                => $this->id,
            'poster'            => $this->poster,
            'subject'           => $this->subject,
            'message'           => $this->message,
            'truncated_message' => $this->getTruncatedMessage(),
            'timestamp'         => $this->timestamp,
            'board_id'          => $this->board_id,
            'parent_id'         => $this->parent_id,
            'updated_at'        => $this->updated_at,
            'estimate'          => $this->estimate,
            'media'             => $this->getMedia()
        ];
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getMedia(): array
    {
        $media = new Media($this->message);

        return $media->getMedias();
    }

    public function getTruncatedMessage(): string
    {
        $message = preg_replace('/https?:\/\/www\.youtube\.com\/watch\?v=([0-9a-z_-]+)/mi', '', $this->message);
        $message = preg_replace('/https?:\/\/youtu\.be\/([0-9a-z_-]+)/mi', '', $this->message);
        $message = preg_replace('/(?!\\!\[[a-z]+\]\()(?<![\'|"])(https:\/\/pbs\.twimg\.com\/media\/[a-z0-9?=&]+|https?:\/\/[a-z.\0-9-_]+\.(jpg|jpeg?|gif|png)(\?[a-z=&0-9]+)?)(?<![\'|"])$(?!\))/mi', '', $this->message);

        return $message;
    }
}
