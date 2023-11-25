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

	$query = "SELECT * FROM accounts WHERE username = '$username' AND password = '$password';";

	$query2 = "SELECT password FROM accounts WHERE username = '$username';";
	$stmt2 = $mydb->query($query2);	

	$stmt = $mydb->query($query);
	if ($stmt->num_rows>0) 
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
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("dbRabbitMQ.ini","baseServer");

echo "dbServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "dbServer END".PHP_EOL;
exit();
?>

