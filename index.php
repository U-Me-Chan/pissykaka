<?php

use Medoo\Medoo;
use PK\Router;
use PK\Application;
use PK\Http\Request;
use PK\Http\Response;
use PK\Database\BoardRepository;
use PK\Database\PostRepository;
use PK\Controllers\BoardsFetcher;
use PK\Controllers\PostFetcher;
use PK\Controllers\PostCreator;
use PK\Controllers\PostDeleter;
use PK\Controllers\PostBoardFetcher;

use PK\Boards\BoardStorage;
use PK\Posts\PostStorage;
use PK\Boards\Controllers\GetBoardList;
use PK\Posts\Controllers\GetThread;
use PK\Posts\Controllers\GetThreadList;
use PK\Posts\Controllers\CreateThread;
use PK\Posts\Controllers\CreateReply;
use PK\Posts\Controllers\UpdatePost;
use PK\Posts\Controllers\DeletePost;
use PK\Passports\Controllers\CreatePassport;

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

$app['router']->addRoute('GET', '/board/all', new BoardsFetcher($board_repo, $app['db']));
$app['router']->addRoute('GET', '/board/{tag}', new PostBoardFetcher($board_repo, $post_repo));

$app['router']->addRoute('GET', '/post/{id:[0-9]+}', new PostFetcher($post_repo));
$app['router']->addRoute('POST', '/post', new PostCreator($post_repo, $board_repo));
$app['router']->addRoute('DELETE', '/post/{id:[0-9]+}', new PostDeleter($post_repo));

$r = $app['router'];

$board_storage = new BoardStorage($app['db']);
$post_storage = new PostStorage($app['db'], $board_storage);

$r->addRoute('GET', '/v2/board', new GetBoardList($board_storage));
$r->addRoute('GET', '/v2/board/{tags:[a-z\+]+}', new GetThreadList($post_storage));

$r->addRoute('GET', '/v2/post/{id:[0-9]+}', new GetThread($post_storage));
$r->addRoute('POST', '/v2/post', new CreateThread($board_storage, $post_storage));
$r->addRoute('PUT', '/v2/post/{id:[0-9]+}', new CreateReply($post_storage));
$r->addRoute('PATCH', 'v2/post/{id:[0-9]+}', new UpdatePost($post_storage, $config['maintenance_key']));
$r->addRoute('DELETE', '/v2/post/{id:[0-9]+}', new DeletePost($post_storage, $config['maintenance_key']));

$r->addRoute('POST', '/v2/passport', new CreatePassport($app['db']));

$app->run();
