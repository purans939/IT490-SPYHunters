<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('testRabbitMQ.ini');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$username = $_SESSION['username'];
$symbol = $_POST['symbol'];
$side = $_POST['side'];
$quantity = $_POST['quantity'];
$ordertype = $_POST['ordertype'];
$price = $_POST['price'];

$request = array();
$request['type'] = "order";
$request['username'] = $username;
$request['message'] = $msg;

$request['symbol'] = $symbol;
$request['side'] = $side;
$request['quantity'] = $quantity;
$request['ordertype'] = $ordertype;
$request['price'] = $price;
$response = $client->send_request($request);
//$response = $client->publish($request);


$payload = json_encode($response);
echo $payload;
?>
