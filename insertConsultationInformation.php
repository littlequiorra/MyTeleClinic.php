<?php
// Establish database connection - Replace these values with your actual database credentials
$servername = "localhost";
$username = "root";
$password = "";
$database = "teleclinic";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set header for JSON response
header('Content-Type: application/json');

// Handle the incoming POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consultationID = $_GET['consultationID'] ?? 0; // Replace 0 with a default value if needed
    $consultationTreatment = $_POST['consultationTreatment'] ?? '';
    $consultationSymptom = $_POST['consultationSymptom'] ?? '';
    $feesConsultation = $_POST['feesConsultation'] ?? '';


    // Your SQL query to update data in the database
    $sql = "UPDATE consultation 
            SET consultationTreatment = '$consultationTreatment', consultationSymptom = '$consultationSymptom' , feesConsultation = '$feesConsultation' 
            WHERE consultationID = '$consultationID'";

    if ($conn->query($sql) === TRUE) {
        // Data updated successfully
        echo json_encode(['status' => 'success', 'message' => 'Data updated successfully']);
    } else {
        // Handle errors
        echo json_encode(['status' => 'error', 'message' => 'Failed to update consultation data']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
