<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teleclinic";
$table = "consultation";

// Check if the patientID is provided in the request
if (isset($_GET['consultationID'])) {
    $consultationID = $_GET['consultationID'];

    // Create Connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check Connection
    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    }

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT * FROM $table WHERE consultationID = ?  ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $consultationID);

    $db_data = array();

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $db_data[] = $row;
        }
        // Send back the complete records as JSON
        echo json_encode($db_data);
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "consultationID parameter is missing";
}
?>
