<?php

use PHPUnit\Framework\TestCase;
use PK\Database\Post\Post;

class PostTest extends TestCase
{
    public function testGetTruncatedMessage(): void
    {
        $post = new Post(0, '', '', 'https://files.catbox.moe/bbaeya.png
https://files.catbox.moe/bbaeya.jpe
https://files.catbox.moe/bbaeya.jpeg
https://pbs.twimg.com/media/E7BQtpkX0AYEzb0?format=jpg&name=large
https://sun9-69.userapi.com/impg/K_6WAH0Cm-ZFiygwA77J8iX0MMI58sMm0sVXiw/_N27D23mMG0.jpg?size=1280x720&quality=96&sign=4940c663c545fb6cdeac518f21449500&type=album
https://pbs.twimg.com/media/E7BQtpkX0AYEzb0?format=jpg&name=large', time(), 1, null, time());

        $this->assertEquals('




', $post->getTruncatedMessage());
    }
}
