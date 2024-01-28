<?php

use App\rabbitmq\Connection;
use PhpAmqpLib\Message\AMQPMessage;

require __DIR__ . "/../../vendor/autoload.php";

$connection = Connection::getInstance();
$channel = $connection->channel();
$channel->queue_declare('produto_registrado', auto_delete: false);
$channel->basic_consume('produto_registrado', callback: function (AMQPMessage $message) {
    echo $message->getBody() . PHP_EOL;
    $message->ack();
});

try {
    $channel->consume();
} catch (Throwable $throwable) {
    var_dump($throwable);
}