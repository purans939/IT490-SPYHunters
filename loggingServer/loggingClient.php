#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$logging = new rabbitMQClient("loggingRabbitMQ.ini","loggingQueue");

$logMsg = array();
$logMSG['type'] = "logger";
$logMSG['machine'] = "VM: Rabbit/DB";
$logMSG['location'] = "Login";
$logMSG['error'] = "Cannot connect to DB - ";
$logging->publish($logMSG);

echo "Error sent to logger".PHP_EOL;
//print_r($logging);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

