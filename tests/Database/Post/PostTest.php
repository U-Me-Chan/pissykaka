<?php

use PHPUnit\Framework\TestCase;
use PK\Database\Post\Post;

class PostTest extends TestCase
{
    public function testGetTruncatedMessage(): void
    {
        $post = new Post(0, '', '', 'https://files.catbox.moe/bbaeya.png
https://files.catbox.moe/bbaeya.jpe
https://files.catbox.moe/bbaeya.jpeg http://youtu.be/tesu
[![](http://filestore.scheoble.xyz/files/thumb.621956dd1738c.gif)](http://filestore.scheoble.xyz/files/621956dd1738c.jpg)
https://pbs.twimg.com/media/E7BQtpkX0AYEzb0?format=jpg&name=large
https://sun9-69.userapi.com/impg/K_6WAH0Cm-ZFiygwA77J8iX0MMI58sMm0sVXiw/_N27D23mMG0.jpg?size=1280x720&quality=96&sign=4940c663c545fb6cdeac518f21449500&type=album
https://pbs.twimg.com/media/E7BQtpkX0AYEzb0?format=jpg&name=large', 1, 1, null, 1);



        $this->assertEquals([
            'id' => 0,
            'poster' => '',
            'subject' => '',
            'message' => 'https://files.catbox.moe/bbaeya.png
https://files.catbox.moe/bbaeya.jpe
https://files.catbox.moe/bbaeya.jpeg http://youtu.be/tesu
[![](http://filestore.scheoble.xyz/files/thumb.621956dd1738c.gif)](http://filestore.scheoble.xyz/files/621956dd1738c.jpg)
https://pbs.twimg.com/media/E7BQtpkX0AYEzb0?format=jpg&name=large
https://sun9-69.userapi.com/impg/K_6WAH0Cm-ZFiygwA77J8iX0MMI58sMm0sVXiw/_N27D23mMG0.jpg?size=1280x720&quality=96&sign=4940c663c545fb6cdeac518f21449500&type=album
https://pbs.twimg.com/media/E7BQtpkX0AYEzb0?format=jpg&name=large',
            'truncated_message' => '

 



',
            'timestamp' => 1,
            'board_id' => 1,
            'parent_id' => null,
            'updated_at' => 1,
            'estimate' => 0,
            'media' => [
                'images' => [
                    [
                        'link' => 'http://filestore.scheoble.xyz/files/621956dd1738c.jpg',
                        'preview' => 'http://filestore.scheoble.xyz/files/thumb.621956dd1738c.gif'
                    ],
                    [
                        'link' => 'https://files.catbox.moe/bbaeya.png',
                        'preview' => 'https://files.catbox.moe/bbaeya.png'
                    ],
                    [
                        'link' => 'https://files.catbox.moe/bbaeya.jpe',
                        'preview' => 'https://files.catbox.moe/bbaeya.jpe'
                    ],
                    [
                        'link' => 'https://files.catbox.moe/bbaeya.jpeg',
                        'preview' => 'https://files.catbox.moe/bbaeya.jpeg'
                    ],
                    [
                        'link' => 'https://sun9-69.userapi.com/impg/K_6WAH0Cm-ZFiygwA77J8iX0MMI58sMm0sVXiw/_N27D23mMG0.jpg?size=1280x720&quality=96&sign=4940c663c545fb6cdeac518f21449500&type=album',
                        'preview' => 'https://sun9-69.userapi.com/impg/K_6WAH0Cm-ZFiygwA77J8iX0MMI58sMm0sVXiw/_N27D23mMG0.jpg?size=1280x720&quality=96&sign=4940c663c545fb6cdeac518f21449500&type=album'
                    ],
                    [
                        'link' => 'https://pbs.twimg.com/media/E7BQtpkX0AYEzb0?format=jpg&name=large',
                        'preview' => 'https://pbs.twimg.com/media/E7BQtpkX0AYEzb0?format=jpg&name=large'
                    ]
                ],
                'youtubes' => [
                    [
                        'link' => 'https://youtu.be/tesu',
                        'preview' => 'https://i1.ytimg.com/vi/tesu/hqdefault.jpg'
                    ]
                ]
            ]
        ], $post->toArray());
    }
}
