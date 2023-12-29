<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teleclinic";

// Create Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Get the specialistID and patientName from the GET parameters
        $specialistID = isset($_GET['specialistID']) ? $_GET['specialistID'] : null;
        $patientName = isset($_GET['patientName']) ? $_GET['patientName'] : null;

        if ($specialistID !== null && $patientName !== null) {
            $stmt = $conn->prepare("SELECT consultation.*, patient.* FROM consultation INNER JOIN patient ON consultation.patientID = patient.patientID WHERE consultation.specialistID = ? AND consultation.consultationStatus = 'Accepted' AND patientName LIKE ? GROUP BY consultation.patientID");
            $stmt->bind_param("is", $specialistID, $patientName); // Adjusted to bind two variables
            $stmt->execute();
            $result = $stmt->get_result();
            $response = $result->fetch_all(MYSQLI_ASSOC);
            http_response_code(200);
            echo json_encode($response);
        } else {
            // Return an error if specialistID or patientName is not provided
            http_response_code(400);
            echo json_encode(array('error' => 'Specialist ID and Patient Name are required.'));
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response = array('error' => 'Error occurred ' . $ee->getMessage());
        echo json_encode($response);
    }
}
?>
