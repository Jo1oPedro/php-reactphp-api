<?php

namespace App\rabbitmq;

use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqManager
{
    public static function publishMessage(string $queueName, string $message)
    {
        $connection = Connection::getInstance();
        $channel = $connection->channel();
        $channel->queue_declare($queueName, auto_delete: false);
        $message = new AMQPMessage($message);
        $channel->basic_publish($message, '', $queueName);
        $channel->close();
        $connection->close();
    }
}