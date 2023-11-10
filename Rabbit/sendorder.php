<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


$client = new rabbitMQClient("baseRabbitMQ.ini","baseServer");

//pulling price

$json = file_get_contents('https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=IBM&apikey=OKNV0XCQX6NO5GJD');
$data = json_decode($json,true);

$values = [];
foreach ($data as $ar) {
        $values[] = $ar['01. symbol'];
        $values[] = $ar['05. price'];
}
$symbol = $ar['01. symbol'];
$price = $ar['05. symbol'];

//pulling price

$msg = 'test';

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
