<?php
// starts the session
session_start();
$userID = $_SESSION['UserID'];
$userName = $_SESSION['UserName'];
$userAdmin = $_SESSION['Admin'];

//database info
$dbServerName = "localhost";
$dbUserName = "db user name";
$dbPassword = "db password";
$dbName = "db name";

$conn = mysqli_connect($dbServerName, $dbUserName, $dbPassword, $dbName);
// Check connection
if (!$conn) {
   die(mysqli_connect_error());
} else {
	//needed to porpery search (searching 'cafe' will return anything with 'Café')
	mysqli_set_charset($conn,  'utf8');
//	echo "<script>console.log(\"Connection successful\");</script>";
}

function closeDB() {
	global $conn;
	mysqli_close($conn);
	echo '<script>console.log("Connection closed")</script>';
}

?>