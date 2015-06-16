<?php
include_once('vars.php');

// Connecting to the mysql server
$conn = new mysqli($servername, $username, $password);

// Checking if connected properly
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else {
	echo "Connected to DB!\n";
	
	// Using the wanted DB
	$conn->query("use SportHeads");
}
?>