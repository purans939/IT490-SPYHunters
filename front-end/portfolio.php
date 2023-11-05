<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Create a RabbitMQ client instance
$client = new rabbitMQClient("baseRabbitMQ.ini","baseServer");

// Check if a custom message is provided as a command-line argument
if (isset($argv[1])) {
    $msg = $argv[1];
} else {
    $msg = "test message";
}

// Prepare the request
$request = array();
$request['type'] = "populate_table";
$request['username'] = "abaseTest";
$request['password'] = "basePW";
$request['message'] = $msg;

// Specify table population parameters
$request['table_name'] = 'your_table_name';  // Replace with the actual table name
$request['data'] = [
    ['column1' => 'value1', 'column2' => 'value2'],
    ['column1' => 'value3', 'column2' => 'value4'],
    // Add more rows as needed
];

// Send the request to RabbitMQ
$response = $client->send_request($request);
//$response = $client->publish($request);

// Check the response and handle it as needed
$payload = json_encode($response);
echo $payload;
?>
