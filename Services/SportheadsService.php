<?php
include_once('vars.php');

// Check if POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get the wanted service
    $serv = isset($_POST['serv']) ? $_POST['serv'] : "";
    
    if ($serv == "get_heads" && isset($_POST['nor'])) {
	    $numOfRequests = $_POST['nor'] * 10;

        // Connecting to the DB
        $conn = new mysqli($servername, $username, $password);
        
        // Checks if the connection succeeded
        if ($conn->connect_error)
        {
            die("Connection failed: " . $conn->connect_error);
        }
        else
        {
            // Using the wanted DB
            $conn->query("use SportHeads");
            
            // Getting the first 10 heads
            $headsQuery = $conn->query("SELECT * FROM heads ORDER BY item_date DESC LIMIT 10 OFFSET " . $numOfRequests);
            
            // Turning query results into array
            $headsArray = array();
            while ($row = mysqli_fetch_assoc($headsQuery)) {
                $headsArray[] = $row;
            }
            
            header('Content-Type: application/json');
            
            // Sending results as JSON
            echo json_encode($headsArray);
        }
    }
    else if ($serv == "get_new_items" && isset($_POST['most_recent'])) {
        $mostRecent = $_POST['most_recent']);

        // Connecting to the DB
        $conn = new mysqli($servername, $username, $password);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        else {
            // Using the wanted DB
            $conn->query("use SportHeads");

            // Getting all the new items
            $headsQuery = $conn->query("SELECT * FROM heads ORDER BY item_date DESC WHERE date_entered > " . $mostRecent);

            // Turning query results into array
            $headsArray = array();
            while ($row = mysqli_fetch_assoc($headsQuery)) {
                $headsArray[] = $row;
            }
            
            header('Content-Type: application/json');
            
            // Sending results as JSON
            echo json_encode($headsArray);
        }
    }
}
?>