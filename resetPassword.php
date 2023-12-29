<?php
$db = mysqli_connect('localhost', 'root', '', 'teleclinic');

if (!$db) {
    echo "Database connection failed";
}

$phone = $_POST['phone'];
$password = $_POST['password'];

$sql_patient = "SELECT * FROM patient WHERE phone = '$phone'";
$result_patient = mysqli_query($db, $sql_patient);
$count = mysqli_num_rows($result_patient);

if ($count == 1) {
    // Update the password
    $sql_updatePassword = "UPDATE patient SET password = '$password' WHERE phone = '$phone'";

    if (mysqli_query($db, $sql_updatePassword)) {
        echo json_encode("success reset");
    } else {
        echo json_encode("error updating password");
    }
} else {
    echo json_encode("error");
}

mysqli_close($db);
?>
