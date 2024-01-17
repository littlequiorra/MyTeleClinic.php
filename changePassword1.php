<?php

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

// Receive updated password data from the request
$patientID = $_POST['patientID'];
$oldPassword = $_POST['oldPassword'];
$newPassword = $_POST['newPassword'];

// Check if the submitted old password matches the existing password
$sqlCheck = "SELECT * FROM patient WHERE patientID = $patientID AND password = '$oldPassword'";
$resultCheck = $conn->query($sqlCheck);

$response = array(); // Create an associative array for the response

if ($resultCheck->num_rows > 0) {
    // Old password matches, update the user's password in the database
    $sqlUpdate = "UPDATE patient SET password = '$newPassword' WHERE patientID = $patientID";

    if ($conn->query($sqlUpdate) === TRUE) {
        $response['success'] = true;
        $response['message'] = "Password updated successfully";
    } else {
        $response['success'] = false;
        $response['message'] = "Error updating password: " . $conn->error;
    }
} else {
    $response['success'] = false;
    $response['message'] = "Old password is incorrect";
}

// Close the database connection
$conn->close();

echo json_encode($response);

?>