<?php
$db = mysqli_connect('localhost', 'root', '', 'teleclinic');

if (!$db) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$specialistID = $_GET['specialistID'];

$update_status_sql = "UPDATE specialist SET logStatus = 'OFFLINE' WHERE specialistID = $specialistID";
$result = mysqli_query($db, $update_status_sql);

if ($result) {
    echo json_encode(["status" => "success", "message" => "Logged out successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error updating log status: " . mysqli_error($db)]);
}

mysqli_close($db);
?>
