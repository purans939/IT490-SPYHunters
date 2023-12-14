<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("dbRabbitMQ.ini","baseServer");

$request = array();
$request['type'] = "order";
$request['username'] = "emailtest";

$request['symbol'] = 'SPY';
$request['side'] = 'buy';
$request['quantity'] = '5';
$request['ordertype'] = 'limit';
$request['price'] = '500';
$request['limitPrice'] = 350;
$request['stopPrice'] = 670;

$response = $client->send_request($request);
//$response = $client->publish($request);

$payload = json_encode($response);
echo $payload;
