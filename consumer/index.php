<?php

require_once __DIR__.'/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
$exchange = 'rabbit_exchange';
$queue = 'default';
$consumerTag = 'consumer';

$host = "jackal.rmq.cloudamqp.com";
$port = "5672";
$user = "oocdtcdw";
$pass = "Tb_UJ_k8IjyC_KXyo4_7apIsEKGKghO2";
$vhost = "oocdtcdw";

$connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
$channel = $connection->channel();

$channel->queue_declare($queue, false, true, false, false);

$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
$channel->queue_bind($queue, $exchange); //bind signica "grudar" a fila com a exchange

function process_message($message)
{
    echo "<pre>";
    $msg = json_decode($message->body, true);
    print_r($msg);
    echo "</pre>";
    //basic ack significa msg lida com sucesso e essa msg vai sair da fila
    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

    if($message->body === 'quit') {
        $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
    }
}

$channel->basic_consume($queue, $consumerTag, false, false, false, false, 'process_message');

function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}

register_shutdown_function('shutdown', $channel, $connection);
while ($channel->is_consuming()) {
    $channel->wait();
}
