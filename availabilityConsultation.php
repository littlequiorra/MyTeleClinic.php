<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teleclinic";
$table = "consultation"; // Assuming the table name is 'consultation'

// Create Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Assuming you're receiving specialist ID and date-time parameters in the query string
    $specialistID = $_GET['specialistID'];
    $consultationDateTime = $_GET['consultationDateTime'];

    // SQL query to check if the specialist is available at the given date-time
    $sql = "SELECT * FROM $table WHERE specialistID = '$specialistID' AND consultationDateTime = '$consultationDateTime'";

    // Execute the query
    $result = $conn->query($sql);

    // Check if any results are returned
    if ($result->num_rows > 0) {
        // Specialist is not available
        echo json_encode(array('available' => false));
    } else {
        // Specialist is available
        echo json_encode(array('available' => true));
    }
}
?>
