#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function predictions($symbol){
try{	
$curl = curl_init();

curl_setopt_array($curl, [
        CURLOPT_URL => "https://stockpred1.p.rapidapi.com/Predictions/7DaysStocksPredictions/".$symbol,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: stockpred1.p.rapidapi.com",
                "X-RapidAPI-Key: a8bc4e395emsh02c49edfac39b36p135fe5jsna7277db8171c"
        ],
]);


$response = curl_exec($curl);
$err = curl_error($curl);
$phpresponse = json_decode($response, true);

curl_close($curl);}
catch(exception){
if ($err) {
	echo "cURL Error #:" . $err;
	$logging = new rabbitMQClient("loggingRabbitMQ.ini","loggingQueue");

$logMsg = array();
$logMSG['type'] = "logger";
$logMSG['machine'] = "VM: Rabbit/DB";
$logMSG['location'] = "Login";
$logMSG['error'] = "Cannot connect to DB - ";
$logging->publish($logMSG);
} else {
        return $phpresponse;
}}


}

function doLogin($username,$password)
{
    // lookup username in databas
    // check password
    return true;
    //return false if not valid
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
	  return "ERROR: unsupported message type";
	  $logging = new rabbitMQClient("loggingRabbitMQ.ini","loggingQueue");

$logMsg = array();
$logMSG['type'] = "logger";
$logMSG['machine'] = "VM: Rabbit/DB";
$logMSG['location'] = "Login";
$logMSG['error'] = "Cannot connect to DB - ";
$logging->publish($logMSG);
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
	    return doValidate($request['sessionId']);
	   case "predictions":
		   return predictions($request['symbol']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","dmzServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

