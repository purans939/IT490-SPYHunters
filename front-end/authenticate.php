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

$username = $_POST['username'];
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$_SESSION['username'] = $_POST['username'];

$request = array();
$request['type'] = "login";
$request['username'] = $username;
$request['password'] = $password;
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
if ($response == 'login success'){
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

	$request['type'] = "create2FA";
	$request['username'] = $username;
	$response = $client->publish($request);
	header('Location: 2fa.php');
}

else{
	header('Location: index.html');
}


echo "\n\n";
    
?>
