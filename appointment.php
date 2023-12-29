<?php
$hostname = "localhost";  // Update this to the correct server address
$database = "teleclinic";
$username = "root";
$password = "";

$db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
http_response_code(404);
$response = new stdClass();

$jsonbody = json_decode(file_get_contents('php://input'));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle POST request (create new appointment)
    try {
        $stmt = $db->prepare("INSERT INTO appointment (patientID, appointmentDateTime,specialistID) VALUES 
                              (:patientID, :appointmentDateTime , :specialistID )");
        $stmt->execute(array(
            ':patientID' => $jsonbody->patientID,
            ':appointmentDateTime' => $jsonbody->appointmentDateTime,
            ':specialistID' => $jsonbody->specialistID,
        ));
        http_response_code(200);
        $response->message = "Appointment created successfully.";
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred: " . $ee->getMessage();
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Handle GET request (retrieve all appointments)
    try {
        $stmt = $db->prepare("SELECT * FROM appointment ORDER BY appointmentDateTime DESC");
        $stmt->execute();
        $response->data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred: " . $ee->getMessage();
    }
}
 else if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Handle PUT request (update existing appointment)
    try {
        if ($jsonbody) {
            $stmt = $db->prepare("UPDATE appointment SET patientID = :patientID,  appointmentDateTime = :appointmentDateTime WHERE appointmentID = :appointmentID");
            $stmt->execute(array(
                ':patientID' => $jsonbody->patientID,
                ':appointmentDateTime' => $jsonbody->appointmentDateTime,
                ':appointmentID' => $jsonbody->appointmentID,
                ':specialistID'=> $jsonbody->specialistID
            ));
            http_response_code(200);
            $response->message = "Appointment updated successfully.";
        } else {
            http_response_code(400);  // Bad Request
            $response->error = "Invalid JSON format in the request body.";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred: " . $ee->getMessage();
    }
} else if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    try {
        if ($jsonbody && isset($jsonbody->appointmentID)) {
            $stmt = $db->prepare("DELETE FROM appointment WHERE appointmentID = :appointmentID");
            $stmt->execute(array(
                ':appointmentID' => $jsonbody->appointmentID
            ));

            // Check if any row was affected
            $rowsAffected = $stmt->rowCount();

            if ($rowsAffected > 0) {
                http_response_code(200);
                $response->message = "Appointment deleted successfully.";
            } else {
                http_response_code(404);  // Not Found
                $response->error = "Appointment with given ID not found.";
            }
        } else {
            http_response_code(400);  // Bad Request
            $response->error = "Invalid or missing 'appointmentID' in the request body.";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred: " . $ee->getMessage();
    }
}

echo json_encode($response);
exit();
?>