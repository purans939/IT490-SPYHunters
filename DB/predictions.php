<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
$username = 'admin';
$symbol = 'AAPL';

$client = new rabbitMQClient("dbRabbitMQ.ini","baseServer");

if (isset($argv[1])) {
    $msg = $argv[1];
} else {
    $msg = "test message";
}

$request = array();
$request['type'] = "predictions";
$request['username'] = "$username";
$request['symbol'] = $symbol;
$request['message'] = $msg;

$response = $client->send_request($request);
$payload = json_encode($response);
echo $payload;

