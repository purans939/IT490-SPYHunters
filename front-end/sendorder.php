<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


$client = new rabbitMQClient("baseRabbitMQ.ini","baseServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
	$msg = "test message";
	$logging = new rabbitMQClient("loggingRabbitMQ.ini","loggingQueue");

$logMsg = array();
$logMSG['type'] = "logger";
$logMSG['machine'] = "VM: Rabbit/DB";
$logMSG['location'] = "Login";
$logMSG['error'] = "Cannot connect to DB - ";
$logging->publish($logMSG);
}

$request = array();
$request['type'] = "order";
$request['username'] = "abaseTest";
$request['password'] = "basePW";
$request['message'] = $msg;

$request['symbol'] = '$SPY';
$request['side'] = 'buy';
$request['quantity'] = '5';
$request['ordertype'] = 'market';
$request['price'] = 'current';
$response = $client->send_request($request);
//$response = $client->publish($request);


$payload = json_encode($response);
echo $payload;
