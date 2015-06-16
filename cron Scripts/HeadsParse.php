<?php
include_once('vars.php');

// Downloading XML
$oneRssFeed = simplexml_load_file("http://www.one.co.il/cat/coop/xml/rss/newsfeed.aspx") or 
              die("Error: Cannot download XML");
echo "Downloaded XML!\n";

// Connecting to the DB
$conn = new mysqli($servername, $username, $password);

// Checks if the connection succeeded
if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}
else
{
    echo "Connected to DB!\n";
    
    // Using the wanted DB
    $conn->query("use SportHeads");
    
    // Going through each item
    foreach ($oneRssFeed->xpath("//item") as $item)
    {   
        // Parsing the CDATA
        $imgLink = explode("\"", explode("src=\"", $item->description)[1])[0];
        $imgDesc = explode("\"", explode("alt=\"", $item->description)[1])[0];
        $itemDesc = explode("/>", $item->description)[1];

        // Fixing the CDATA
        $imgDesc = html_entity_decode($imgDesc, ENT_QUOTES, 'UTF-8');
        $itemDesc = html_entity_decode($itemDesc, ENT_QUOTES, 'UTF-8');

        // Fixing the image link
        $imgLink = str_replace("small", "ms", $imgLink);
        $imgLink = str_replace("gif", "jpg", $imgLink);

        // Checks if the item already exists
        $existingItem = $conn->query("SELECT * FROM heads WHERE item_guid = " . $item->guid);
        if ($existingItem->num_rows != 0)
        {
            echo "Item " . $item->guid . " already exists!\n";

            // Creating the update query
            $updateItemQuery = "UPDATE heads" .
                              " SET item_title = '" . $conn->real_escape_string($item->title) . 
                                "', item_desc = '" . $conn->real_escape_string($itemDesc) . 
                                "', img_link = '" . $imgLink . 
                                "', img_desc = '" . $conn->real_escape_string($imgDesc) . 
                                "', item_link = '" . $item->link . 
                                "', item_date = '" . date('Y-m-d H:i:s', strtotime($item->pubDate)) . "'" .
                              " WHERE item_guid = " . $item->guid;

            // Checks if any items updated
            if ($conn->query($updateItemQuery) === TRUE)
            {
                echo "Item " . $item->guid . " updated!\n";
            }
            else
            {
                echo "Item " . $item->guid . " failed to update!\n" . $conn->error . "\n";
            }
        }
        else
        {
            // Creating the INSERT query
            $addItemQuery = "INSERT INTO heads" .
                           " VALUES (" . $item->guid . ",'" . $conn->real_escape_string($item->title) . 
                                 "','" . $conn->real_escape_string($itemDesc) . "','" . $imgLink . 
                                 "','" . $conn->real_escape_string($imgDesc) . "','" . $item->link . 
                                 "','" . date('Y-m-d H:i:s', strtotime($item->pubDate)) . "'," .
                                 "(SELECT NOW())" . ")";

            // Checks the result of the query and prints the result
            if ($conn->query($addItemQuery) === TRUE)
            {
                echo "Added Item!\n";
            }
            else
            {
                echo "Adding Item failed\n" . $conn->error . "\n";
            }
        }

        // Closing the last result set
        $existingItem->close();
    }
    
    // Closes the connection
    $conn->close();
    echo "End of job!\n";
}
?>