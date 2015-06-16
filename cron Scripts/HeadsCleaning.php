<?php
// Including global vars
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

	// Checking if the query was successful
	if ($conn->query("DELETE FROM heads WHERE item_date < ADDDATE(NOW(), -30)") === TRUE) {
		echo "All items older than 30 days successfully removed!\n";
	} else {
		echo "Error in cleaning items older than 30 days.\n" . $conn->error . "\n";
	}

	// Closing the connection
	$conn->close();
	echo "End of job!\n";
}
?>