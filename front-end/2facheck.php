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

$twofa = $_POST['2fa'];
$username = $_SESSION['username'];
$_SESSION['2fa'] = $_POST['2fa'];

$request = array();
$request['type'] = "check2FA";
$request['inputCode'] = $twofa;
$request['2fa'] = $twofa;
$request['username'] = $username;
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
if ($response == 'login success'){
        header('Location: home.php');
}

else{
        header('Location: index.html');
}


echo "\n\n";

?> 
