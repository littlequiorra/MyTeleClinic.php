<?php

// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "teleclinic";

// Create a connection
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate a random channel name
function generateRandomString($length) {
    $charset = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $result = '';
    $random = rand(0, strlen($charset) - 1);

    for ($i = 0; $i < $length; $i++) {
        $result .= $charset[($random + $i) % strlen($charset)];
    }

    return $result;
}

// Function to get the channel name
function getChannelName($consultationID) {
    global $conn;

    // Check if a channel already exists for the given consultation
    $sql = "SELECT channelName FROM consultation WHERE consultationID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $consultationID);
    $stmt->execute();
    $stmt->bind_result($channelName);
    $stmt->fetch();
    $stmt->close();

    if ($channelName) {
        // Channel already exists, return the existing channel name
        return $channelName;
    } else {
        // No need to insert a new row here
        return null;
    }
}

// Main logic

// Assuming you receive the consultation ID as a GET parameter
$consultationID = $_GET['consultationID'];

// Get the channel name for the given consultation ID
$channelName = getChannelName($consultationID);

// Return the channel name as JSON response
echo json_encode(['channelName' => $channelName]);

// Close the database connection
$conn->close();

?>
