<?php

$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

$query = "INSERT INTO portfolio (username, symbol, side, quantity, ordertype, price) VALUES ('$username', '$symbol', '$side', '$quantity', '$ordertype', '$price')";

        echo "Order has been entered";
       // return "Order has been entered";
$stmt = $mydb->query($query);

