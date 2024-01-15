<?php
$hostname = "localhost";
$database = "teleclinic";
$username = "root";
$password = "";

$db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
http_response_code(404); // Set initial response code

$response = new stdClass();
$jsonbody = json_decode(file_get_contents('php://input'));

try {
    $consultationID = isset($_GET['consultationID']) ? $_GET['consultationID'] : null;

    if ($consultationID !== null) {
        $stmt = $db->prepare("SELECT medicine.MedGeneral, medicine.medForm, medicine.dosage, medication.medInstruction, consultation.consultationDateTime
                             FROM ((medication
                             INNER JOIN consultation ON medication.consultationID = consultation.consultationID)
                             INNER JOIN medicine ON medication.MedID = medicine.MedID)
                             WHERE consultation.consultationID = :consultationID");
        $stmt->bindParam(':consultationID', $consultationID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $historyConsultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response->data = $historyConsultations;
            $response->success = true;
            http_response_code(200); // Set success response code

            // Output the JSON response with a "data" key
            echo json_encode(['data' => $response]);
        } else {
            $response->success = false;
            $response->error = "Error retrieving history consultations: " . $stmt->errorInfo()[2];
        }
    } else {
        $response->error = "ConsultationID is missing in the request.";
        $response->success = false;
    }
} catch (Exception $ee) {
    http_response_code(500);
    $response->error = "Error occurred " . $ee->getMessage();
}

// Remove any additional echoes or outputs here

exit();
?>
