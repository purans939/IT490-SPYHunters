<?php

session_start();

?>

<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>SPYHunters</h1>
				<a href="home.php"><i class="fas fa-user-circle"></i>Home</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Home Page</h2>
			<form action="orderentry.php" method="POST">
        <!-- String Field (1-4 characters) -->
        <label for="symbol">Symbol (1-4 characters):</label>
        <input type="text" id="symbol" name="symbol" pattern="[A-Za-z]{1,4}" required><br>

        <!-- Dropdown Menu for Buy/Sell -->
        <label for="side">Side:</label>
        <select id="side" name="side" required>
            <option value="buy">Buy</option>
            <option value="sell">Sell</option>
        </select><br>

        <!-- Quantity (positive integer above 0) -->
        <label for="quantity">Quantity (positive integer):</label>
        <input type="number" id="quantity" name="quantity" min="1" step="1" required><br>

        <!-- Dropdown Menu for Order Type -->
	<label for="ordertype">Order Type:</label>
	<select id="ordertype" name="ordertype" required onchange="showAdditionalFields()">
    	<option value="market">Market</option>
    	<option value="limit">Limit</option>
    	<option value="stop">Stop</option>
	</select><br>

	<!-- Additional Field for Limit Price -->
	<div id="limitPriceField" style="display: none;">
    	<label for="limitPrice">Limit Price:</label>
    	<input type="number" id="limitPrice" name="limitPrice" min="0" step="0.01">
	</div>

	<!-- Additional Field for Stop Price -->
	<div id="stopPriceField" style="display: none;">
   	 <label for="stopPrice">Stop Price:</label>
    	<input type="number" id="stopPrice" name="stopPrice" min="0" step="0.01">
	</div><br>

        <!-- Submit Button -->
        <input type="submit" value="Submit">
    </form>
		</div>
	</body> </html>

	<script>
    function showAdditionalFields() {
        var orderType = document.getElementById("ordertype").value;
        var limitPriceField = document.getElementById("limitPriceField");
        var stopPriceField = document.getElementById("stopPriceField");

        if (orderType === "limit" || orderType === "stop") {
            limitPriceField.style.display = "block";
            stopPriceField.style.display = "block";
        } else {
            limitPriceField.style.display = "none";
            stopPriceField.style.display = "none";
        }
    }
	</script>


