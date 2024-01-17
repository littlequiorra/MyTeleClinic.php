<?php
header('Content-Type: application/json'); // Set content type to JSON

$db = mysqli_connect('localhost', 'root', '', 'teleclinic');

if (!$db) {
    echo json_encode(array("message" => "Database connection failed"));
    exit;
}

$phone = $_POST['phone'];
$password = $_POST['password'];

// Check if the phone exists in the patient table
$sql_checkPatient = "SELECT * FROM patient WHERE phone = '$phone'";
$result_checkPatient = mysqli_query($db, $sql_checkPatient);
$count_patient = mysqli_num_rows($result_checkPatient);

// Check if the phone exists in the specialist table
$sql_checkSpecialist = "SELECT * FROM specialist WHERE phone = '$phone'";
$result_checkSpecialist = mysqli_query($db, $sql_checkSpecialist);
$count_specialist = mysqli_num_rows($result_checkSpecialist);

if ($count_patient > 0 || $count_specialist > 0) {
    // Update the password based on the table where the phone is found
    if ($count_patient > 0) {
        $sql_updatePassword = "UPDATE patient SET password = '$password' WHERE phone = '$phone'";
    } elseif ($count_specialist > 0) {
        $sql_updatePassword = "UPDATE specialist SET password = '$password' WHERE phone = '$phone'";
    }

    if (mysqli_query($db, $sql_updatePassword)) {
        echo json_encode(array("message" => "success reset"));
    } else {
        echo json_encode(array("message" => "error updating password"));
    }
} else {
    echo json_encode(array("message" => "error"));
}

mysqli_close($db);
?>
