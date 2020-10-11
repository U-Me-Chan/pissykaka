<?php

use PHPUnit\Framework\TestCase;
use PK\Http\Request;

class RequestTest extends TestCase
{
    /**
     * @dataProvider dpParseHeaders
     */
    public function testParseHeaders(array $expected, array $server)
    {
        $req = new Request($server, [], []);

        $this->assertEquals($expected, $req->getHeaders());
    }

    public function dpParseHeaders(): array
    {
        return [
            [
                [
                    'HTTP_COOKIE' => 'test',
                    'HTTP_CONTENT_TYPE' => 'application/json'
                ],
                [
                    'HTTP_COOKIE' => 'test',
                    'HTTP_CONTENT_TYPE' => 'application/json'
                ]
            ]
        ];
    }
}
