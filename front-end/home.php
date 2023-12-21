<!DOCTYPE html>
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
        			<a href="rssfeed.php">News</a>
        			<a href="order.php">Order</a>
        			<a href="portfolio.php">Portfolio</a>
        			<a href="makepred.php">Predictions</a>
        			<a href="tradingview_embed.php">Chart</a>
        			<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>

			</div>
		</nav>
		<div class="content">
			<h2>Home Page</h2>
			<p>Welcome back, <?=$_SESSION['username']?>!</p>
		</div>
	</body>
</html>
