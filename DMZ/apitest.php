<?php
session_start();

$curl = curl_init();

$symbol =  

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

curl_close($curl);

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
	echo $response;
}
