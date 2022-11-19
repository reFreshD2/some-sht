<?php

use App\Application\Chat\Chat;
use App\Application\Chat\Server;
use App\Infrastructure\ProjectManager\ProjectManager;

require_once dirname(__DIR__) . '/autoload.php';

$manager = new ProjectManager(dirname(__DIR__) . '/conf/packages');

/**
 * @var Server $server
 */
$server = $manager->get(Server::class);
/**
 * @var Chat $chat
 */
$chat = $manager->get(Chat::class);

while ($channel = $server->start()) {
    $chat->setChannel($channel);
    $chat->startChat();
    $server->close($channel);
}
