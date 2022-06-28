<?php

namespace PK\Posts\Post;

use PK\Boards\Board\Board;

class Post implements \JsonSerializable
{
    public static function draft(
        Board $board,
        int|null $parent_id,
        string $message,
        string $poster = 'Anonymous',
        string $subject = ''
    ): self {
        return new self(
            0,
            $poster,
            $subject,
            $message,
            time(),
            $board,
            $parent_id,
            time(),
            0,
            hash('sha256', bin2hex(random_bytes(5)))
        );
    }

    public static function fromArray(array $state): self
    {
        return new self(
            $state['id'],
            $state['poster'],
            $state['subject'],
            $state['message'],
            $state['timestamp'],
            Board::fromArray($state['board_data']),
            $state['parent_id'],
            $state['updated_at'],
            $state['estimate'],
            $state['password'],
            !empty($state['replies']) ? $state['replies'] : [],
            isset($state['replies_count']) ? $state['replies_count'] : 0,
            $state['is_verify'] == 'yes' ? true : false
        );
    }

    public function jsonSerialize(): array
    {
        $data = get_object_vars($this);
        $data['board_id'] = $data['board']->id;

        list($media, $truncated_message) = $this->getMediaAndTruncatedMessage();

        $data['media'] = $media;
        $data['truncated_message'] = $truncated_message;
	$data['datetime'] =  date('Y-m-d H:m:s', $data['timestamp'] + 60 * (60 * 4));

        unset($data['password']);

        return $data;
    }

    public function toArray(): array
    {
        $data = get_object_vars($this);
        $data['board_id'] = $data['board']->id;

        unset($data['board'], $data['replies'], $data['replies_count'], $data['is_verify']);

        return $data;
    }

    private function __construct(
        public int $id,
        public string $poster,
        public string $subject,
        public string $message,
        public int $timestamp,
        public Board $board,
        public int|null $parent_id,
        public int $updated_at,
        public int $estimate,
        public string $password,
        public array $replies = [],
        public int $replies_count = 0,
        public bool $is_verify = false
    ) {
    }


    public function getMediaAndTruncatedMessage(): array
    {
        $message = $this->message;
        $media   = $images = $youtubes = [];

        if (preg_match_all('/((`{1,})[\s\S]+?(`{1,}))(*SKIP)(*F)|\[\!\[\]\((?<preview>.+)\)\]\((?<link>.+)\)/mi', $message, $matches)) {
            foreach ($matches['link'] as $k => $link) {
                $images[$link] = [
                    'link' => $link,
                    'preview' => $matches['preview'][$k]
                ];
            }
        }

        $message = preg_replace('/((`{1,})[\s\S]+?(`{1,}))(*SKIP)(*F)|\[\!\[\]\((?<preview>.+)\)\]\((?<link>.+)\)/mi', '', $message);

        if (preg_match_all('/((`{1,})[\s\S]+?(`{1,}))(*SKIP)(*F)|https?\:\/\/[a-z0-9_\-.\/]+\.(jpe?g?|gif|png)(\?[a-z0-9=_\/\-&]+)?/mi', $message, $matches)) {
            foreach ($matches[0] as $link) {
                $images[$link] = [
                    'link' => $link,
                    'preview' => $link
                ];
            }
        }

        $message = preg_replace('/((`{1,})[\s\S]+?(`{1,}))(*SKIP)(*F)|https?\:\/\/[a-z0-9_\-.\/]+\.(jpe?g?|gif|png)(\?[a-z0-9=_\/\-&]+)?/mi', '',  $message);

        if (preg_match_all('/((`{1,})[\s\S]+?(`{1,}))(*SKIP)(*F)|https?\:\/\/pbs\.twimg\.com\/media\/[a-z0-9\?=&]+/mi', $message, $matches)) {
            foreach ($matches[0] as $link) {
                $images[$link] = [
                    'link' => $link,
                    'preview' => $link
                ];
            }
        }

        $message = preg_replace('/((`{1,})[\s\S]+?(`{1,}))(*SKIP)(*F)|https?\:\/\/pbs\.twimg\.com\/media\/[a-z0-9\?=&]+/mi', '', $message);

        if (preg_match_all('/((`{1,})[\s\S]+?(`{1,}))(*SKIP)(*F)|https?:\/\/www\.youtube\.com\/watch\?v=([0-9a-z_-]+)/mi', $message, $matches)) {
            foreach ($matches[4] as $id) {
                $youtubes[$id] = [
                    'link' => "https://youtu.be/{$id}",
                    'preview' => "http://i1.ytimg.com/vi/{$id}/hqdefault.jpg"
                ];
            }
        }

        if (preg_match_all('/((`{1,})[\s\S]+?(`{1,}))(*SKIP)(*F)|https?:\/\/youtu\.be\/([0-9a-z_-]+)/mi', $message, $matches)) {
            foreach ($matches[4] as $id) {
                $youtubes[$id] = [
                    'link' => "https://youtu.be/{$id}",
                    'preview' => "https://i1.ytimg.com/vi/{$id}/hqdefault.jpg"
                ];
            }
        }

        $message = preg_replace('/((`{1,})[\s\S]+?(`{1,}))(*SKIP)(*F)|https?:\/\/www\.youtube\.com\/watch\?v=([0-9a-z_-]+)/mi', '', $message);
        $message = preg_replace('/((`{1,})[\s\S]+?(`{1,}))(*SKIP)(*F)|https?:\/\/youtu\.be\/([0-9a-z_-]+)/mi', '', $message);

        $data = [
            'images' => array_values($images),
            'youtubes' => array_values($youtubes)
        ];

        return [$data, $message];
    }
}
