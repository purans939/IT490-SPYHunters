<?php
session_start();

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('testRabbitMQ.ini');


echo "First part of script";

$client = new rabbitMQClient("/var/www/spyweb/rabbitmq/rabbitmqphp_example/testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

echo "second part of script";

$request = array();
$request['type'] = "Login";
$request['username'] = 'Bill';
$request['password'] = 'password';
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;



?>
