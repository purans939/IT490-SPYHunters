#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require '/home/ps1messaging/git/testDB/vendor/autoload.php';

//$client = new rabbitMQClient("dbRabbitMQ.ini","dmzServer");

function doLogin($username,$password)
{
	$logging = new rabbitMQClient("dbRabbitMQ.ini", "loggingQueue");
	$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');
	

if ($mydb->errno != 0)
{
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	
        $logging = new rabbitMQClient("dbRabbitMQ.ini", "loggingQueue");	
	$logMsg = array();
	$logMSG['type']='logger';
	$logMSG['machine'] = "VM: Rabbit/DB";
	$logMSG['location'] = "Login";
	$logMSG['error'] = "Cannot connect to DB - " . $mydb->error;
	$logging->publish($logMSG);

	exit(0);
}

	echo "successfully connected to database".PHP_EOL;

	$query2 = "SELECT password FROM accounts WHERE username = '$username';";
	$stmt2 = $mydb->query($query2);	

	$query = "SELECT * FROM accounts WHERE username = '$username'";
	$stmt = $mydb->query($query);
	
	//grabbing pw from db
	$result = mysqli_query($mydb, $query2);
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$dbQPW=$row['password']; }
	}
	//grabbing pw from db

	if ($stmt->num_rows>0)	
	{
		if (password_verify($password, $dbQPW))  
		{	
			echo "login success";
			$msg = "login success";
			return $msg;	
			//exit(0);
		}
		else if ($password != $query2)
		{
		        $logging = new rabbitMQClient("dbRabbitMQ.ini", "loggingQueue");
			$logMsg = array();
	        	$logMSG['type']='logger';
        		$logMSG['machine'] = "VM: Rabbit/DB";
        		$logMSG['location'] = "Login";
        		$logMSG['error'] = "Incorrect password " . $mydb->error;
        		$logging->publish($logMSG);

			echo "incorrect pw";
			$msg = "incorrect pw";
			return $msg;
		}
		else
		{
		        $logging = new rabbitMQClient("dbRabbitMQ.ini", "loggingQueue");
			$logMsg = array();
	       		$logMSG['type']='logger';
	       		$logMSG['machine'] = "VM: Rabbit/DB";
       			$logMSG['location'] = "Login";
        		$logMSG['error'] = "Incorrect username " . $mydb->error;
      			$logging->publish($logMSG);

                	echo "username not found";
                	$msg = "username not found";
                	return $msg;
        	}	
	}
	else
	{
		$logging = new rabbitMQClient("dbRabbitMQ.ini", "loggingQueue");
                $logMsg = array();
                $logMSG['type']='logger';
                $logMSG['machine'] = "VM: Rabbit/DB";
                $logMSG['location'] = "Login";
                $logMSG['error'] = "Incorrect password " . $mydb->error;
                $logging->publish($logMSG);
		return 'pw couldnt be verified';
	}	
}

function createUser($username,$password)
{
$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        $logging = new rabbitMQClient("dbRabbitMQ.ini", "loggingQueue");	
	$logMsg = array();
	$logMSG['type']='logger';
	$logMSG['machine'] = "VM: Rabbit/DB";
	$logMSG['location'] = "Login";
	$logMSG['error'] = "Cannot connect to DB - " . $mydb->error;
	$logging->publish($logMSG);
        exit(0);
}

echo "successfully connected to database".PHP_EOL;

$query = "INSERT INTO accounts (username, password) VALUES ('$username', '$password')";
$response = $mydb->query($query);

	echo "Account created";
	return "Account has been created";
 	//return false if not valid
}

function addSession ($username) {

$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        $logging = new rabbitMQClient("dbRabbitMQ.ini", "loggingQueue");	
	$logMsg = array();
	$logMSG['type']='logger';
	$logMSG['machine'] = "VM: Rabbit/DB";
	$logMSG['location'] = "Login";
	$logMSG['error'] = "Adding session" . $mydb->error;
	$logging->publish($logMSG);
        exit(0);
}

echo "successfully connected to database".PHP_EOL;

$query = "INSERT INTO sessions (username) VALUES ('$username')";
$response = $mydb->query($query);

}

function delSession ($username) {

$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        $logging = new rabbitMQClient("dbRabbitMQ.ini", "loggingQueue");	
	$logMsg = array();
	$logMSG['type']='logger';
	$logMSG['machine'] = "VM: Rabbit/DB";
	$logMSG['location'] = "Login";
	$logMSG['error'] = "Deleting session" . $mydb->error;
	$logging->publish($logMSG);
        exit(0);
}

echo "successfully connected to database".PHP_EOL;

$query = "DELETE FROM sessions WHERE username = $username";
$response = $mydb->query($query);

}

function doValidate ($sessionID) {

$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        $logging = new rabbitMQClient("dbRabbitMQ.ini", "loggingQueue");	
	$logMsg = array();
	$logMSG['type']='logger';
	$logMSG['machine'] = "VM: Rabbit/DB";
	$logMSG['location'] = "Validate";
	$logMSG['error'] = "Cannot connect to DB - " . $mydb->error;
	$logging->publish($logMSG);
        exit(0);
}

echo "successfully connected to database".PHP_EOL;

$query = "SELECT * FROM sessions WHERE id = '$sessionID';";

$stmt = $mydb->query($query);
if ($stmt->num_rows>0) 
{
        echo "Validation successful".PHP_EOL;
        //exit(0);
}
else
{
        echo "Session cannot be validated";
}
    return true;
    //return false if not valid

}

function orderEntry($username,$symbol,$side,$quantity,$ordertype,$price,$stopPrice,$limitPrice)
{
	
	//market order types
	if ($ordertype=='market'){
		$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');
		$query = "INSERT INTO portfolio (username, symbol, side, quantity, ordertype, price) VALUES ('$username', '$symbol', '$side', '$quantity', '$ordertype', '$price')";
		$response = $mydb->query($query);
		
		echo "Order has been entered";
        	return "Order has been entered";
	}
	elseif ($limitPrice < $price){
			$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');
			$query = "INSERT INTO portfolio (username, symbol, side, quantity, ordertype, price) VALUES ('$username', '$symbol', '$side', '$quantity', '$ordertype', '$price')";
			$response = $mydb->query($query);
			
			echo "Limit order has been entered";
        		return "Limit order has been entered";
		}
	elseif ($price < $limitPrice){
			$orderDB = new mysqli('127.0.0.1','baseUser','12345','baseDB');
			$orderQ = "INSERT INTO awaitingorders (username, symbol, quantity, price, limitOrder, stopOrder) VALUES ('$username', '$symbol', '$quantity', '$price', '$limitPrice', '$stopPrice');";
			$response = $orderDB->query($orderQ);
			
			echo "Order unfulfilled - awaiting limit";
        		return "Order unfulfilled - awaiting limit";
		}	
}

function sendPortfolio ($username) {

$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        exit(0);
}

echo "successfully connected to database".PHP_EOL;

$query = "SELECT * FROM portfolio WHERE username = '$username';";

$stmt = $mydb->query($query);

//call port
$callPort=[];

while($row = $stmt->fetch_assoc()) {
	$callPort[] = $row;
	//print_r($callPort);
};

//call port


if ($stmt->num_rows>0)
{
        echo "Portfolio sent".PHP_EOL;
	return $callPort; 
	
}
else
{
        echo "error on portfolio";
        $logging = new rabbitMQClient("dbRabbitMQ.ini", "loggingQueue");	
	$logMsg = array();
	$logMSG['type']='logger';
	$logMSG['machine'] = "VM: Rabbit/DB";
	$logMSG['location'] = "Sending portfolio";
	$logMSG['error'] = "Cannot pull portfolio of user:" . $username . PHP_EOL;
	$logging->publish($logMSG);
        
}
    return true;
    //return false if not valid

}

function sendPredictions ($symbol) {

//send client to dmz asking for data
$client = new rabbitMQClient("dbRabbitMQ.ini","dmzServer");
$request = array();
$request['type'] = "predictions";
$request['symbol'] = $symbol;
$response = $client->send_request($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

//add response into db + if symbol is in there, update rows

foreach ($response as $row) {
	$columns = 'Symbol, ' . implode(", ", array_keys($row));
	echo $columns;
	$values = "'" . $symbol . "'" . ", " . "'" . implode("', '", $row) . "'";
	echo $values;
	//$query2 = "INSERT INTO predictions (symbol, datetime, open, high, low, close, volume, adjusted) VALUES (1, 2, 3, 4, 5, 6, 7, 8)";
	$query2 = "INSERT INTO predictions ($columns) VALUES ($values)";
	
$mydb2 = new mysqli('127.0.0.1','baseUser','12345','baseDB');
if ($mydb2->errno != 0) {
        echo "failed to connect to database: ". $mydb2->error . PHP_EOL;
        exit(0); }
echo "successfully connected to database".PHP_EOL;
$stmt2 = $mydb2->query($query2);
	}

//db
$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');
if ($mydb->errno != 0) {
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        exit(0); }
echo "successfully connected to database".PHP_EOL;
$query = "SELECT * FROM predictions WHERE symbol = '$symbol';";
$stmt = $mydb->query($query);

//call prediction of symbol from db
$callPrediction=[];
while($row = $stmt->fetch_assoc()) {
        $callPrediction[] = $row;
        //print_r($callPrediction);
        };

//send prediction back to frontend
if ($stmt->num_rows>0)
{
        echo "Prediction sent".PHP_EOL;
        return $callPrediction;
}
else
{
        echo "error on prediction";
}
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
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
    case "createUser":
      return createUser($request['username'],$request['password']);	      
    case "order":
      return orderEntry($request['username'], $request['symbol'], $request['side'], $request['quantity'], $request['ordertype'], $request['price'], $request['limitPrice'], $request['stopPrice']);
    case "portfolio":
      return sendPortfolio($request['username']);
    case "predictions":
      return sendPredictions($request['symbol']);      
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("dbRabbitMQ.ini","baseServer");

echo "dbServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "dbServer END".PHP_EOL;
exit();
?>

