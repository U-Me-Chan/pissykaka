<?php

use PHPUnit\Framework\TestCase;
use PK\Database\Post\Media;

class MediaTest extends TestCase
{
    public function testGetYoutubeLinks(): void
    {
        $media = new Media("https://www.youtube.com/watch?v=Pbl0QGr7NVw \n https://youtu.be/Pbl0QGr7NVw https://www.youtube.com/watch?v=gn77SqE8BXk");

        $this->assertEquals([
            [
                'link' => 'https://youtu.be/Pbl0QGr7NVw',
                'preview' => 'http://i1.ytimg.com/vi/Pbl0QGr7NVw/hqdefault.jpg'
            ],
            [
                'link' => 'https://youtu.be/gn77SqE8BXk',
                'preview' => 'http://i1.ytimg.com/vi/gn77SqE8BXk/hqdefault.jpg'
            ]
        ], $media->getYoutubeLinks());
    }

    public function testGetImages(): void
    {
        $media = new Media("http://filestore.scheoble.xyz/files/thumb.620c2c817e87b.jpg

ahttp://filestore.scheoble.xyz/files/thumb.61f810fa56b78.jpg

https://sun9-69.userapi.com/impg/K_6WAH0Cm-ZFiygwA77J8iX0MMI58sMm0sVXiw/_N27D23mMG0.jpg?size=1280x720&quality=96&sign=4940c663c545fb6cdeac518f21449500&type=album

https://pbs.twimg.com/media/E7BQtpkX0AYEzb0?format=jpg&name=large");

        $this->assertEquals([
            [
                'link' => 'http://filestore.scheoble.xyz/files/thumb.620c2c817e87b.jpg',
                'preview' => 'http://filestore.scheoble.xyz/files/thumb.620c2c817e87b.jpg'
            ],
            [
                'link' => 'http://filestore.scheoble.xyz/files/thumb.61f810fa56b78.jpg',
                'preview' => 'http://filestore.scheoble.xyz/files/thumb.61f810fa56b78.jpg'
            ],
            [
                'link' => 'https://sun9-69.userapi.com/impg/K_6WAH0Cm-ZFiygwA77J8iX0MMI58sMm0sVXiw/_N27D23mMG0.jpg?size=1280x720&quality=96&sign=4940c663c545fb6cdeac518f21449500&type=album',
                'preview' => 'https://sun9-69.userapi.com/impg/K_6WAH0Cm-ZFiygwA77J8iX0MMI58sMm0sVXiw/_N27D23mMG0.jpg?size=1280x720&quality=96&sign=4940c663c545fb6cdeac518f21449500&type=album'
            ],
            [
                'link' => 'https://pbs.twimg.com/media/E7BQtpkX0AYEzb0?format=jpg&name=large',
                'preview' => 'https://pbs.twimg.com/media/E7BQtpkX0AYEzb0?format=jpg&name=large'
            ]
        ], $media->getImages());
    }
}
