#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username,$password)
{
	$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

if ($mydb->errno != 0)
{
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
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
			echo "incorrect pw";
			$msg = "incorrect pw";
			return $msg;
		}
		else
        	{
                	echo "username not found";
                	$msg = "username not found";
                	return $msg;
        	}	
	}
	else
	{
		return 'pw couldnt be verified';
	}	
}

function createUser($username,$password)
{
$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
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

function orderEntry($username,$symbol,$side,$quantity,$ordertype,$price)
{
$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        exit(0);
}

echo "successfully connected to database".PHP_EOL;

//market order

$query = "INSERT INTO portfolio (username, symbol, side, quantity, ordertype, price) VALUES ('$username', '$symbol', '$side', '$quantity', '$ordertype', '$price')";
$response = $mydb->query($query);

        echo "Order has been entered";
        return "Order has been entered";
        //return false if not valid
}

//market order

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
      return orderEntry($request['username'], $request['symbol'], $request['side'], $request['quantity'], $request['ordertype'], $request['price']);
    case "portfolio":
      return sendPortfolio($request['username']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("dbRabbitMQ.ini","baseServer");

echo "dbServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "dbServer END".PHP_EOL;
exit();
?>

