<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('testRabbitMQ.ini');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
//pulling price
$symbol = $_POST['symbol'];
$url = 'https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol='.$symbol.'&apikey=NEOQOL1NQUI8AUO8';
$json = file_get_contents($url);
$data = json_decode($json,true);

echo $json;

$values = [];
foreach ($data as $ar) {
        $values[] = $ar['01. symbol'];
	$values[] = $ar['05. price'];
}
$symbolVal = $ar['01. symbol'];
$priceVal = $ar['05. price'];

//pulling price

$msg = 'test';

$username = $_SESSION['username'];
$side = $_POST['side'];
$quantity = $_POST['quantity'];
$ordertype = $_POST['ordertype'];
//$price = $_POST['price'];

$limitPrice = isset($_POST['limitPrice']) ? $_POST['limitPrice'] : null;
$stopPrice = isset($_POST['stopPrice']) ? $_POST['stopPrice'] : null;

$request = array();
$request['type'] = "order";
$request['username'] = $username;
$request['message'] = $msg;

$request['symbol'] = $symbol;
$request['side'] = $side;
$request['quantity'] = $quantity;
$request['ordertype'] = $ordertype;
$request['price'] = $priceVal;
$request['limitPrice'] = $limitPrice;
$request['stopPrice'] = $stopPrice;
$response = $client->send_request($request);
//$response = $client->publish($request);


$payload = json_encode($response);
echo $payload;
?>
