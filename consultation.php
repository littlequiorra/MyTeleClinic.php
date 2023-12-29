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
        $stmt->execute(array(':consultationID' => $jsonbody->consultationID, ':patientID' => $jsonbody->patientID, ':consultationDateTime' => $jsonbody->consultationDateTime,
            ':specialistID' => $jsonbody->specialistID, ':consultationStatus' => $jsonbody->consultationStatus));
        http_response_code(200);
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $stmt = $db->prepare("SELECT c.*, s.specialistName FROM consultation c JOIN specialist s ON c.specialistID = s.specialistID");
        $stmt->execute();
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
}else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $stmt = $db->prepare("SELECT * FROM consultation WHERE patientID=? AND specialistID=?");
        $stmt->execute();
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
}
echo json_encode($response);
exit();
?>