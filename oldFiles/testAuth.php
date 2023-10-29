<?php
session_start();

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	$client = new rabbitMQClient("/var/www/spyweb/rabbitmq/rabbitmqphp_example/testRabbitMQ.ini", "testServer");
	$request = array();
	$request['type'] = "login";
	$request['username'] = $_POST["username"];
        $request['password'] = $_POST["password"];
	$response = $client->send_request($request);

}

?>

