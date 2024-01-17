<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$hostname = "localhost";
$database = "teleclinic";
$username = "root";
$password = "";

$response = new stdClass();

header('Content-Type: application/json');

try {
    // Create a MySQLi connection
    $conn = new mysqli($hostname, $username, $password, $database);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the consultationID is provided
    if (isset($_REQUEST['consultationID'])) {
        $consultationID = $_REQUEST['consultationID'];

        // Handle both POST and GET requests
        $dynamicCallID = '';
        $specialistID = '';
        $specialistName = '';

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            // Prepare and execute the SQL statement to retrieve the dynamicCallID, specialistID, and specialistName
            $stmtSelect = $conn->prepare("SELECT c.dynamicCallID, c.specialistID, s.specialistName 
                                          FROM consultation c 
                                          JOIN specialist s ON c.specialistID = s.specialistID
                                          WHERE c.consultationID = ?");
            $stmtSelect->bind_param("i", $consultationID);

            if ($stmtSelect->execute()) {
                $stmtSelect->bind_result($dynamicCallID, $specialistID, $specialistName);

                if ($stmtSelect->fetch()) {
                    $response->dynamicCallID = $dynamicCallID;
                    $response->specialistID = $specialistID;
                    $response->specialistName = $specialistName;

                    $response->success = true;
                } else {
                    $response->success = false;
                    $response->error = "Consultation ID not found";
                }
            } else {
                $response->success = false;
                $response->error = "Error retrieving data: " . $stmtSelect->error;
            }

            $stmtSelect->close();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Check if the dynamicCallID is provided in the POST data
            if (isset($_POST['dynamicCallID'])) {
                $dynamicCallID = $_POST['dynamicCallID'];

                // Prepare and execute the SQL statement to update the dynamicCallID
                $stmtUpdate = $conn->prepare("UPDATE consultation SET dynamicCallID = ? WHERE consultationID = ?");
                $stmtUpdate->bind_param("si", $dynamicCallID, $consultationID);

                if ($stmtUpdate->execute()) {
                    $response->success = true;
                } else {
                    $response->success = false;
                    $response->error = "Error updating dynamicCallID: " . $stmtUpdate->error;
                }

                $stmtUpdate->close();
            } else {
                $response->success = false;
                $response->error = "dynamicCallID not provided in POST data";
            }
        } else {
            http_response_code(405);
            $response->error = "Method not allowed";
        }
    } else {
        http_response_code(400);
        $response->error = "Consultation ID not provided";
    }

    // Close the connection
    $conn->close();
} catch (Exception $ee) {
    http_response_code(500);
    $response->error = "Error occurred " . $ee->getMessage();
}

// Echo only the JSON-encoded response
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit();
?>
