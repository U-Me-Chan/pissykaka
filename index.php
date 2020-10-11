<?php

use PK\Http\Request;
use PK\Http\Response;
use PK\Router;
use PK\Application;
use Medoo\Medoo;
use PK\Exceptions\Http\NotFound;

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

$app['router']->addRoute('POST', '/post', function (Request $req) {
    if (!isset($req->getParams()['board_name'])) {
        return (new Response([], 400))->setException(new \InvalidArgumentException('Не задано имя доски, которой принадлежит поcт'));
    }

    $board_data = Application::$app['db']->get('boards', '*', ['name' => (string) $req->getParams()['board_name']]);
    $board_id = (int) $board_data['id'];
    $poster    = isset($req->getParams()['poster']) ? $req->getParams()['poster'] : 'Anonymous';
    $subject   = isset($req->getParams()['subject']) ? $req->getParams()['subject'] : '';
    $message   = isset($req->getParams()['message']) ? $req->getParams()['message'] : '';
    $timestamp = time();
    $parent_id = isset($req->getParams()['parent_id']) ? $req->getParams()['parent_id'] : null;

    Application::$app['db']->insert('posts', [
        'poster' => $poster,
        'subject' => $subject,
        'message' => $message,
        'timestamp' => $timestamp,
        'board_id' => $board_id,
        'parent_id' => $parent_id
    ]);

    return new Response(['id' => Application::$app['db']->id()], 201);
});

$app['router']->addRoute('DELETE', '/post', function (Request $req) {
    if (!isset($req->getParams()['id'])) {
        return (new Response([], 400))->setException(new \InvalidArgumentException('Не задан идентификатор поста для удаления'));
    }

    Application::$app['db']->delete('posts', ['AND' => ['id' => $req->getParams()['id']]]);

    return new Response([], 204);
});

$app['router']->addRoute('GET', '/post', function (Request $req) {
    if (!isset($req->getParams()['id'])) {
        return (new Response([], 404))->setException(new NotFound());
    }

    $thread_data = Application::$app['db']->select('posts', '*', ['id' => (int) $req->getParams()['id']]);
    $thread_data['replies'] = Application::$app['db']->select('posts', '*', ['parent_id' => (int) $req->getParams()['id']]);

    return new Response(['thread_data' => $thread_data]);
});

$app['router']->addRoute('GET', '/board', function (Request $req) {
    if (!isset($req->getParams()['name'])) {
        return (new Response([], 404))->setException(new NotFound());
    }

    $board_data = Application::$app['db']->get('boards', '*', ['name' => (string) $req->getParams()['name']]);
    $board_data['threads'] = Application::$app['db']->select(
        'posts',
        '*',
        [
            'AND' => [
                'board_id' => (int) $board_data['id'],
                'parent_id' => null
            ]
        ]
    );

    return new Response(['board_data' => $board_data]);
});

$app->run();
