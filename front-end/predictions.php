<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
$username = 'testusername';
$symbol = $_POST['symbol'];
// Create a RabbitMQ client instance
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

// Check if a custom message is provided as a command-line argument

// Prepare the request
$request = array();
$request['type'] = "predictions";
$request['username'] = "$username";
$request['symbol'] = $symbol;
$request['message'] = $msg;


if (!$request){
$logging = new rabbitMQClient("testRabbitMQ.ini","loggingQueue");

$logMsg = array();
$logMSG['type'] = "logger";
$logMSG['machine'] = "VM: WebServer";
$logMSG['location'] = "predictions";
$logMSG['error'] = "Cannot send predictions client - ";
$logging->publish($logMSG);
}


/*
 * // Specify table population parameters
$request['table_name'] = 'your_table_name';  // Replace with the actual table name
$request['data'] = [
    ['column1' => 'value1', 'column2' => 'value2'],
    ['column1' => 'value3', 'column2' => 'value4'],
    // Add more rows as needed
];
 */
// Send the request to RabbitMQ
$response = $client->send_request($request);
//$response = $client->publish($request);

// Check the response and handle it as needed
$payload = json_encode($response);
echo $payload;
?>
