<?php

namespace PK;

use Pimple\Container;

class Application extends Container
{
    public static $app;

    public function __construct(array $config)
    {
        $this->app = $this;
        $this->app['config'] = $config;
        self::$app = $this;
    }

    public function run(): void
    {
        $res = $this['router']->handle($this['request']);

        if (!empty($res->getHeaders())) {
            foreach ($this['request']->getHeaders() as $header) {
                header($header);
            }
        }

        header('Content-type: application/json');
        http_response_code($res->getCode());
        echo json_encode($res->getBody());

        exit(0);
    }
}
