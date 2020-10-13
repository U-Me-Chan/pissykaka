<?php

use PK\Http\Request;
use PK\Http\Response;
use PK\Router;
use PK\Application;
use Medoo\Medoo;
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
        'password' => $app['config']['db']['password']
    ]);
};

$board_repo = new BoardRepository($app['db']);
$post_repo  = new PostRepository($app['db']);

$app['router']->addRoute('GET', '/board/all', new BoardsFetcher($board_repo));

try {
    $boards = $board_repo->fetch();

    foreach ($boards as $board) {
        $app['router']->addRoute('GET', sprintf('/board/%s', $board->getTag()), new PostBoardFetcher($board_repo, $post_repo));

        try {
            $posts = $post_repo->findByBoardId($board->getId());

            foreach ($posts as $post) {
                $app['router']->addRoute('GET', sprintf('/post/%s', $post->getId()), new PostFetcher($post_repo));
            }
        } catch (PostNotFound $e) {
        }
    }
} catch (BoardNotFound $e) {
}

$app['router']->addRoute('POST', '/post', new PostCreator($post_repo, $board_repo));
$app['router']->addRoute('DELETE', '/post', new PostDeleter($post_repo));

$app->run();
