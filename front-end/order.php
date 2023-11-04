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

        <!-- Dropdown Menu for Market -->
        <label for="ordertype">Order Type:</label>
        <select id="ordertype" name="ordertype" required>
            <option value="market">Market</option>
        </select><br>

        <!-- Input Field for Monetary Value -->
        <label for="price">Price (monetary value):</label>
        <input type="number" id="price" name="price" min="0" step="0.01" required><br>

        <!-- Submit Button -->
        <input type="submit" value="Submit">
    </form>
		</div>
	</body>
</html>
