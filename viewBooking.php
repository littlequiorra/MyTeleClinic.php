<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teleclinic";
$table = "appointment";

// Create Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
    return;
}

$patientID = $_GET['patientID']; // Assuming patientID is passed as a parameter

// Get all records from the database for a specific patientID
//$sql = "SELECT * FROM consultation WHERE patientID='$patientID'";

//$sql = "SELECT c.*, s.specialistName FROM consultation c JOIN specialist s ON c.specialistID = s.specialistID WHERE patientID='$patientID'";

$sql = "SELECT c.*, s.specialistName 
        FROM consultation c 
        JOIN specialist s ON c.specialistID = s.specialistID 
        WHERE patientID='$patientID'";

$db_data = array();

$result = $conn->query($sql);

if ($result === false) {
    http_response_code(500); // Internal Server Error
    echo "Error executing query: " . $conn->error;
} else {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $db_data[] = $row;
        }
        // Send back the complete records as JSON
        echo json_encode($db_data);
    } else {
        echo "No data found for this patientID";
    }
}

$conn->close();
return;
?>
