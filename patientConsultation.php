<?php
$hostname = "localhost";
$database = "teleclinic";
$username = "root";
$password = "";

$db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
//initial response code
//response code will be changed if the request goes into any of the process
http_response_code(404);
$response = new stdClass();

$jsonbody = json_decode(file_get_contents('php://input'));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $db->prepare("INSERT INTO consultation (`consultationID`, `patientID`, `consultationDateTime`, `specialistID`, `consultationStatus`)
                            VALUES (:consultationID, :patientID, :consultationDateTime, :specialistID, :consultationStatus)");
        $stmt->execute(array(
            ':consultationID' => $jsonbody->consultationID,
            ':patientID' => $jsonbody->patientID,
            ':consultationDateTime' => $jsonbody->consultationDateTime,
            ':specialistID' => $jsonbody->specialistID,
            ':consultationStatus' => $jsonbody->consultationStatus
        ));
        http_response_code(200);
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Retrieve patientID and specialistID from the URL
        $patientID = isset($_GET['patientID']) ? $_GET['patientID'] : null;
        $specialistID = isset($_GET['specialistID']) ? $_GET['specialistID'] : null;

        // Check if both parameters are present
        if ($patientID !== null && $specialistID !== null) {
            $stmt = $db->prepare("SELECT * FROM consultation WHERE patientID=:patientID AND specialistID=:specialistID AND consultationStatus = 'Done'");
            $stmt->bindParam(':patientID', $patientID, PDO::PARAM_INT);
            $stmt->bindParam(':specialistID', $specialistID, PDO::PARAM_INT);
            $stmt->execute();

            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
        } else {
            http_response_code(400); // Bad Request
            $response->error = "Both patientID and specialistID are required.";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
}
echo json_encode($response);
exit();
?>
