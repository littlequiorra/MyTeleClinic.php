<?php
$db = mysqli_connect('localhost', 'root', '', 'teleclinic');

if (!$db) {
    echo "Database connection failed";
}

$patientName = $_POST['patientName'];
$phone = $_POST['phone'];
$password = $_POST['password'];

$sql = "SELECT phone FROM patient WHERE phone = '".$phone."'";
$result = mysqli_query($db, $sql);
$count = mysqli_num_rows($result);

if ($count == 1) {
    echo json_encode("error");

} else {
     
    $insert = "INSERT INTO patient( patientName,phone, password) VALUES (
   '".$patientName."','".$phone."','".$password."')";

    $query = mysqli_query($db, $insert);
if($query){


    echo json_encode("successful");
}
}
?>
