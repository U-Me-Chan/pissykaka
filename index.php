<?php

use Medoo\Medoo;
use FastRoute\RouteParser\Std as RouteParser;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher\GroupCountBased as RouteDispatcher;
use PK\Http\Request;
use PK\Http\Response;
use PK\Router;
use PK\Application;
use PK\Exceptions\Http\NotFound;
use PK\Database\BoardRepository;
use PK\Database\PostRepository;
use PK\Controllers\BoardsFetcher;
use PK\Controllers\PostFetcher;
use PK\Controllers\PostCreator;
use PK\Controllers\PostDeleter;
use PK\Controllers\PostBoardFetcher;
use PK\Exceptions\Board\BoardNotFound;
use PK\Exceptions\Post\PostNotFound;

require_once "vendor/autoload.php";

$config = require "config.php";

$app = new Application($config);

$app['request'] = new Request($_SERVER, $_POST, $_FILES);
$app['router'] = new Router();

$app['db'] = function ($app) {
    return new Medoo([
        'database_type' => 'mysql',
        'database_name' => $app['config']['db']['database'],
        'server' => $app['config']['db']['hostname'],
        'username' => $app['config']['db']['username'],
        'password' => $app['config']['db']['password'],
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ]);
};

$board_repo = new BoardRepository($app['db']);
$post_repo  = new PostRepository($app['db']);

$app['router']->addRoute('GET', '/board/all', new BoardsFetcher($board_repo));
$app['router']->addRoute('GET', '/board/{tag}', new PostBoardFetcher($board_repo, $post_repo));
$app['router']->addRoute('GET', '/post/{id:[0-9]+}', new PostFetcher($post_repo));
$app['router']->addRoute('POST', '/post', new PostCreator($post_repo, $board_repo));
$app['router']->addRoute('DELETE', '/post/{id:[0-9]+}', new PostDeleter($post_repo));

$app->run();
