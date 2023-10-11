<?php
session_start();
// Change this to your connection info.
$DATABASE_HOST = '';
$DATABASE_USER = '';
$DATABASE_PASS = '';
$DATABASE_NAME = '';
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	exit('Please fill both the username and password fields!');
}

$username = $_POST['username'];
$password = $_POST['password'];

// You should sanitize and hash the password for security. This is a basic example; use a more secure method in practice.
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare an SQL query to insert the username and hashed password into your database.
$stmt = $con->prepare("INSERT INTO accounts (username, password) VALUES (?, ?)");

// Check if the prepare statement was successful.
if ($stmt) {
    // Bind the parameters and execute the query.
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        echo 'User registered successfully!';
    } else {
        echo 'Error: ' . $stmt->error;
    }

    // Close the statement.
    $stmt->close();
} else {
    echo 'Error: ' . $con->error;
}

// Close the database connection.
$con->close();
?>