<?php
// Replace with your actual database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teleclinic";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get JSON data from the request body
$data = json_decode(file_get_contents("php://input"), true);

// Extract data
$MedID = $data['MedID'];
$MedInstruction = $data['MedInstruction'];
$consultationID = $data['consultationID'];

// Perform the SQL query to insert data into the database
$sql = "INSERT INTO medication (MedID, MedInstruction, consultationID) VALUES ('$MedID', '$MedInstruction', '$consultationID')";

if ($conn->query($sql) === TRUE) {
    echo "Medication inserted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>
