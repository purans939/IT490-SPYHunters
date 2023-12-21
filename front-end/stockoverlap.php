<?php
// Check if a custom message is provided as a command-line argument
if (isset($argv[1])) {
    $msg = $argv[1];
} else {
        $msg = "test message";
        $logging = new rabbitMQClient("loggingRabbitMQ.ini","loggingQueue");

$logMsg{= array();
$logMSG['type'] = "logger";
$logMSG['machine'] = "VM: Rabbit/DB";
$logMSG['location'] = "Login";
$logMSG['error'] = "Cannot connect to DB - ";
$logging->publish($logMSG);
}

// Prepare the request
$request = array();
$request['type'] = "stockoverlap";
$request['username'] = "$username";
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

