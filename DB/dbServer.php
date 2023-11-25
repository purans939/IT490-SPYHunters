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

function orderEntry($username,$symbol,$side,$quantity,$ordertype,$price,$stopPrice,$limitPrice)
{
$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        exit(0);
}

echo "successfully connected to database".PHP_EOL;

//pull current price
$url = 'https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol='.$symbol.'&apikey=OKNV0XCQX6NO5GJD';
$json = file_get_contents($url);
$data = json_decode($json,true);

$values=[];
foreach ($data as $ar) {
	$values[] = $ar['05. price'];
}
$priceVal = $ar['05. price'];

//market order

if ($ordertype='market') {
	$price = $price;
	}
else if ($ordertype='limit'){
	if ($side='buy'){
		if ($limitPrice > $priceVal) {
			$price = $price;
			$emailMsg = 'Buy limit order fulfilled';
			return 'Buy limit order fulfilled';
		}
		else if ($limitPrice < $priceVal) {
			$emailMsg = 'Price not met, order not fulfilled';
			return 'Price not met, order not fulfilled';
			
			$mydb3 = new mysqli('127.0.0.1','baseUser','12345','baseDB');
			$query = "INSERT INTO awaitingorders (username, symbol, quantity, price, limitOrder, stopOrder) VALUES ('$username', '$symbol', '$quantity', '$price', '$limitPrice', '$stopPrice')";
			$stmt3 = $mydb->query($query);
		}
	}
	else if ($side='sell') {
		if ($limitPrice > $priceVal) {
                        $price = $price;
                        $emailMsg = 'Sell limit order fulfilled';
                        return 'Sell limit order fulfilled';
                }
                else if ($limitPrice < $priceVal) {
                        $emailMsg = 'Price not met, order not fulfilled';
			return 'Price not met, order not fulfilled';

			$mydb3 = new mysqli('127.0.0.1','baseUser','12345','baseDB');
                        $query = "INSERT INTO awaitingorders (username, symbol, quantity, price, limitOrder, stopOrder) VALUES ('$username', '$symbol', '$quantity', '$price', '$limitPrice', '$stopPrice')";
                        $stmt3 = $mydb->query($query);
                }
	}
}
else if ($ordertype='stop') {
        if ($side='buy'){
                if ($stopPrice >= $priceVal) {
                        $price = $price;
                        $emailMsg = 'Buy stop order fulfilled';
                        return 'Buy stop order fulfilled';
                }
                else {
                        $emailMsg = 'Price not met, order not fulfilled';
                        return 'Price not met, order not fulfilled';
			
			$mydb3 = new mysqli('127.0.0.1','baseUser','12345','baseDB');
                        $query = "INSERT INTO awaitingorders (username, symbol, quantity, price, limitOrder, stopOrder) VALUES ('$username', '$symbol', '$quantity', '$price', '$limitPrice', '$stopPrice')";
                        $stmt3 = $mydb->query($query);
		}
        }
        else if ($side='sell') {
                if ($stopPrice <= $priceVal) {
                        $price = $price;
                        $emailMsg = 'Sell stop order fulfilled';
                        return 'Sell stop order fulfilled';
                }
                else {
                        $emailMsg = 'Price not met, order not fulfilled';
                        return 'Price not met, order not fulfilled';
			
			$mydb3 = new mysqli('127.0.0.1','baseUser','12345','baseDB');
                        $query = "INSERT INTO awaitingorders (username, symbol, quantity, price, limitOrder, stopOrder) VALUES ('$username', '$symbol', '$quantity', '$price', '$limitPrice', '$stopPrice')";
                        $stmt3 = $mydb->query($query);	
		}
        }
}	

//market order

// push notifications

	//get email
	
	$mydb2 = new mysqli('127.0.0.1','baseUser','12345','baseDB');
	$query2 = "SELECT email FROM accounts WHERE username = '$username';";
        $stmt2 = $mydb2->query($query2);

        $result = mysqli_query($mydb, $query2);
        if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                        $emailPush=$row['email']; }
		}
        
	//get email

$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->SMTPAuth = true;
$mail->Username = 'spyhunters490@gmail.com';
$mail->Password = 'gxjtdmttmxpypfkw';
$mail->setFrom('spyhunters490@gmail.com', 'SPY Hunters');
$mail->addAddress($emailPush, 'Test Test');
$mail->Subject = 'Order confirmed!';
$mail->Body = $emailMsg;
//$mail->Body = 'Order confirmed: ' . $symbol . ' at price: ' . $price;

//send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message sent!';
        }

// push notifications

$query = "INSERT INTO portfolio (username, symbol, side, quantity, ordertype, price) VALUES ('$username', '$symbol', '$side', '$quantity', '$ordertype', '$price')";

        echo "Order has been entered";
        return "Order has been entered";
$response = $mydb->query($query);

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

