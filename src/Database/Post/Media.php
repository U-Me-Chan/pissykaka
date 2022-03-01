<?php

namespace PK\Database\Post;

class Media
{
    public function __construct(
        private string $message
    ) {
    }

    public function getImages(): array
    {
        $data = [];

        if (preg_match_all('/(?!\\!\[[a-z]+\]\()(?<![\'|"])((?<twilink>https?:\/\/pbs\.twimg\.com\/media\/[a-z0-9?=&]+)|(?<link>https?:\/\/[a-z.\0-9-_]+\.(?<ext>jpg|jpeg?|gif|png)(?<params>\?[a-z=&0-9]+)?))(?<![\'|"])$(?!\))/mi', $this->message, $matches)) {
            foreach ($matches[1] as $link) {
                $data[$link] = [
                    'link' => $link,
                    'preview' => $link
                ];
            }
        }

        return array_values($data);
    }

    public function getYoutubeLinks(): array
    {
        $data = [];

        if (preg_match_all('/https?:\/\/www\.youtube\.com\/watch\?v=([0-9a-z_-]+)/mi', $this->message, $matches)) {
            foreach ($matches[1] as $id) {
                $data[$id] = [
                    'link' => "https://youtu.be/{$id}",
                    'preview' => "http://i1.ytimg.com/vi/{$id}/hqdefault.jpg"
                ];
            }
        }

        if (preg_match_all('/https?:\/\/youtu\.be\/([0-9a-z_-]+)/mi', $this->message, $matches)) {
            foreach ($matches[1] as $id) {
                $data[$id] = [
                    'link' => "https://youtu.be/{$id}",
                    'preview' => "http://i1.ytimg.com/vi/{$id}/hqdefault.jpg"
                ];
            }
        }

        return array_values($data);
    }

    public function getMedias(): array
    {
        $data['images'] = $this->getImages();
        $data['youtube'] = $this->getYoutubeLinks();

        return $data;
    }
}
