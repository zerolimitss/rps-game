<?php

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use App\Game;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Game()
        )
    ),
    8080,
    "127.0.0.1"
);

$server->run();