<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teleclinic";

// Give your table name
$table = "specialist"; // let's create a table named Employees.

// Create Connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check Connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$clinicID = isset($_GET['clinicID']) ? $_GET['clinicID'] : null;

// Use prepared statement to prevent SQL injection
$sql = "SELECT specialistID, clinicID, specialistName, specialistTitle, phone, password, logStatus FROM $table WHERE clinicID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $clinicID); // Assuming clinicID is a string, change the "s" if it's an integer

$db_data = array();

if ($stmt->execute()) {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $db_data[] = $row;
        }
        // Send back the complete records as a json
        echo json_encode($db_data);
    } else {
        echo "No records found";
    }
} else {
    echo "Error executing statement: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
