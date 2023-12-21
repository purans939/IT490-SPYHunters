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
//pull current price

$url = 'https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol='.$symbol.'&apikey=OKNV0XCQX6NO5GJD';
$json = file_get_contents($url);
$data = json_decode($json,true);

$values=[];
foreach ($data as $ar) {
        $values[] = $ar['05. price'];
}

$priceVal = $ar['05. price'];

	settype($limitPrice, "integer");
	settype($stopPrice, "integer");
	
	//market order types
	if ($ordertype=='market'){
		$price = $priceVal;
		
		$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');
		$query = "INSERT INTO portfolio (username, symbol, side, quantity, ordertype, price) VALUES ('$username', '$symbol', '$side', '$quantity', '$ordertype', '$price')";
		$response = $mydb->query($query);
		
		//echo "Order has been entered";
        	//return "Order has been entered";
	}
	else if ($ordertype=='limit' && $price > $limitPrice){
		
			$price = $priceVal;
				
			$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');
			$query = "INSERT INTO portfolio (username, symbol, side, quantity, ordertype, price) VALUES ('$username', '$symbol', '$side', '$quantity', '$ordertype', '$price')";
			$response = $mydb->query($query);
			
			//echo "Limit order has been entered";
        		//return "Limit order has been entered";
		}
	elseif($ordertype=='limit' && $price < $limitPrice){
		echo 'anything here';
			$price = $priceVal;
			$orderDB = new mysqli('127.0.0.1','baseUser','12345','baseDB');
			$orderQ = "INSERT INTO awaitingorders (username, symbol, quantity, price, limitOrder, stopOrder) VALUES ('$username', '$symbol', '$quantity', '$price', '$limitPrice', '$stopPrice');";
			$response = $orderDB->query($orderQ);
			
			//echo "Order unfulfilled - awaiting limit";
        		//return "Order unfulfilled - awaiting limit";
		}	
	elseif ($ordertype=='stop'){
		$priceVal = 459;
		if($stopPrice > $priceVal){    
		        $price = $priceVal;
			
			$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');
			$query = "INSERT INTO portfolio (username, symbol, side, quantity, ordertype, price) VALUES ('$username', '$symbol', '$side', '$quantity', '$ordertype', '$price')";
			$response = $mydb->query($query);
			
			//echo "Stop order has been entered";
        		//return "Stop order has been entered";
		}
		else{
			$price = $priceVal;
			$orderDB = new mysqli('127.0.0.1','baseUser','12345','baseDB');
			$orderQ = "INSERT INTO awaitingorders (username, symbol, quantity, price, limitOrder, stopOrder) VALUES ('$username', '$symbol', '$quantity', '$price', '$limitPrice', '$stopPrice')";
			$response = $orderDB->query($orderQ);
			
			//echo "Order unfulfilled - awaiting stop";
        		//return "Order unfulfilled - awaiting stop";
		}
	}

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
		//$mail->Body = 'test';
		$mail->Body = "Order confirmed: $" . $symbol . "\nPrice: $" . $price . "\nQuantity: " . $quantity . "\nSide: " . $side . "\nOverall order: $" . ($quantity*$price);

		//send the message, check for errors
		if (!$mail->send()) {
		    echo 'Mailer Error: ' . $mail->ErrorInfo;
		    	$logging = new rabbitMQClient("dbRabbitMQ.ini", "loggingQueue");	
			$logMsg = array();
			$logMSG['type']='logger';
			$logMSG['machine'] = "VM: Rabbit/DB";
			$logMSG['location'] = "PHPMailer on Push Notification";
			$logMSG['error'] = "Cannot send email - " . $mail->ErrorInfo;
			$logging->publish($logMSG);
		} else {
		    echo 'Message sent!';
			}
 
// push notifications


        echo "Order has been entered";
        return "Order has been entered";
        //return false if not valid
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
	//echo $columns;
	$values = "'" . $symbol . "'" . ", " . "'" . implode("', '", $row) . "'";
	//echo $values;
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

function create2FA ($username){

	$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

	//generate code
	$code = rand(100000, 999999);
	
	//make sure there is no other code in DB for user
	$checkCode = "SELECT * FROM 2facodes WHERE username = '$username'";
	$stmt = $mydb->query($checkCode);

	if ($stmt->num_rows>0) {
		$removeCode = "DELETE FROM 2facodes WHERE username = '$username'";
		$stmt2 = $mydb->query($removeCode);

		//send to 2facodes db associated to username
		$storeCode = "INSERT INTO 2facodes (username, code) VALUES ('$username', '$code')";
		$stmt3 = $mydb->query($storeCode);
		
			//get email
		
			$mydb2 = new mysqli('127.0.0.1','baseUser','12345','baseDB');
			$query2 = "SELECT email FROM accounts WHERE username = '$username';";
			$stmt2 = $mydb2->query($query2);

			$result = mysqli_query($mydb, $query2);
			if (mysqli_num_rows($result) > 0) {
				while ($row = mysqli_fetch_assoc($result)) {
				        $emailPush=$row['email']; }
				}
			
			//send email
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
			$mail->addAddress($emailPush, $username);
			$mail->Subject = 'Your 2FA Code';
			$mail->Body = 'Your 2FA Code is: ' . $code;
					if (!$mail->send()) {
		    				echo 'Mailer Error: ' . $mail->ErrorInfo;
		  			} 
		  			else {
		    				echo 'Message sent!';
					}
	}
	else {
		//send to 2facodes db associated to username
		$storeCode = "INSERT INTO 2facodes (username, code) VALUES ('$username', '$code')";
                $stmt3 = $mydb->query($storeCode);
                
                //get email
        
		$mydb2 = new mysqli('127.0.0.1','baseUser','12345','baseDB');
		$query2 = "SELECT email FROM accounts WHERE username = '$username';";
		$stmt2 = $mydb2->query($query2);

		$result = mysqli_query($mydb, $query2);
		if (mysqli_num_rows($result) > 0) {
		        while ($row = mysqli_fetch_assoc($result)) {
		                $emailPush=$row['email']; }
		        }
		
		//send email
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
		$mail->addAddress($emailPush, $username);
		$mail->Subject = 'Your 2FA Code';
		$mail->Body = 'Your 2FA Code is: ' . $code;
	}
}

function check2FA ($username, $inputCode) {

        $mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

	//get code from db where username
	$check = "SELECT code FROM 2facodes WHERE username = '$username'";
        $stmt = $mydb->query($check);
		
	//if user input = code in DB, return message
	$result = mysqli_query($mydb, $check);
        if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
			$codeCheck=$row['code']; }
	}	

        //check if inputCode equals result in data
	if ($codeCheck == $inputCode){
		$checkMsg = 'login success';
		return $checkMsg;	
	}
	else {
		$checkMsg = 'incorrect code';
		return $checkMsg;
	}
}

function stockOverlap ($username) {

	//pull portfolio from db
	$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');
		if ($mydb->errno != 0){
			echo "failed to connect to database: ". $mydb->error . PHP_EOL;
			exit(0); }
		echo "successfully connected to database".PHP_EOL;
	$query = "SELECT username, symbol, side, quantity, ordertype, price FROM portfolio WHERE username = '$username';";
	$stmt = $mydb->query($query);

	//call port, take data, evaluate percentages, store in new db
	if ($stmt->num_rows>0) {
	/*
		//get symbol 
		$query2 = "SELECT symbol FROM portfolio GROUP BY symbol;";
		$stmt2 = $mydb->query($query2);
		$getSymbol = $stmt2->fetch_all(PDO::FETCH_ASSOC);
		foreach ($getSymbol as $symbolArr) {
			foreach ($symbolArr as $stockSym) {
				$query5 = "INSERT INTO stockOverlap (username, symbol) VALUES ('$username', '$stockSym');";
				$stmt5 = $mydb->query($query5);
			}
		} */
		
		
		//get percentage - try new method
		//last minute fix - combine all 3 variables into 1 select statement
		$query3 = "SELECT symbol, AVG(price), sum(quantity*price) / (SELECT SUM(quantity*price) FROM portfolio) * 100 AS PERCENTAGE FROM portfolio GROUP BY symbol;";
		$stmt3 = $mydb->query($query3);
		$getPercentage = $stmt3->fetch_all(PDO::FETCH_ASSOC);
		foreach ($getPercentage as $percentageArr) {
			$query6 = "INSERT INTO stockOverlap (username, quantity, avgPrice, symbol) VALUES ('$username', '$percentageArr[2]', '$percentageArr[1]', '$percentageArr[0]');";
			$stmt6 = $mydb->query($query6);
		}
	
		//get avg price
		/*
		$query4 = "SELECT AVG(price) FROM portfolio GROUP BY symbol;";
		$stmt4 = $mydb->query($query4);
		$getAvgPrice = $stmt4->fetch_all(PDO::FETCH_ASSOC);
		foreach ($getAvgPrice as $avgPriceArr) {
			foreach ($avgPriceArr as $stockAvg) {
				$query7 = "UPDATE stockOverlap SET avgPrice = '$stockAvg' WHERE username = '$username';";
				$stmt7 = $mydb->query($query7);
			}
		} */
	}		
	
	//call stats from stockOverlap table
	$query5 = "SELECT Weight FROM stockOverlap WHERE Ticker = '$symbol';";
	$stmt5 = $mydb->query($query5);
	$getSymbolWeight = $stmt5->fetch_all(PDO::FETCH_ASSOC);
	
	

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
    case "create2FA":
      return create2FA($request['username']);	   
    case "check2FA":
      return check2FA($request['username'], $request['inputCode']);
    case "stockOverlap":
      return stockOverlap($request['username']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("dbRabbitMQ.ini","baseServer");

echo "dbServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "dbServer END".PHP_EOL;
exit();
?>

