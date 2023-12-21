#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function loggingRabbitDB($machine,$location,$error)
{

$file = fopen("/var/www/spyhunters/log.txt","a");

$date = date('Y-m-d H:i:s');

fwrite($file, "$date" . "\n");
fwrite($file, "Machine: ". $machine . "\n");
fwrite($file, "Location: ". $location . "\n");
fwrite($file, "Error: ". $error . "\n");

fclose($file);

}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "logger":
      return loggingRabbitDB($request['machine'],$request['location'],$request['error']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","loggingQueue");

echo "loggingServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "loggingServer END".PHP_EOL;
exit();
?>

