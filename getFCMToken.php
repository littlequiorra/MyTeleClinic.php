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
        $fcmToken = '';

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            // Prepare and execute the SQL statement to retrieve the fcmToken
            $stmtSelect = $conn->prepare("SELECT p.fcmToken FROM patient p
                                          JOIN consultation c ON p.patientID = c.patientID
                                          WHERE c.consultationID = ?");
            $stmtSelect->bind_param("i", $consultationID);

            if ($stmtSelect->execute()) {
                $stmtSelect->bind_result($fcmToken);

                if ($stmtSelect->fetch()) {
                    $response->fcmToken = $fcmToken;
                    $response->success = true;
                } else {
                    $response->success = false;
                    $response->error = "consultationID not found";
                }
            } else {
                $response->success = false;
                $response->error = "Error retrieving fcmToken: " . $stmtSelect->error;
            }

            $stmtSelect->close();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Check if the fcmToken is provided in the POST data
            if (isset($_POST['fcmToken'])) {
                $fcmToken = $_POST['fcmToken'];

                // Prepare and execute the SQL statement to update the dynamicCallID
                $stmtUpdate = $conn->prepare("UPDATE patient SET fcmToken = ? WHERE patientID = ?");
                $stmtUpdate->bind_param("si", $fcmToken, $patientID);

                if ($stmtUpdate->execute()) {
                    $response->success = true;
                } else {
                    $response->success = false;
                    $response->error = "Error updating fcmToken: " . $stmtUpdate->error;
                }

                $stmtUpdate->close();
            } else {
                $response->success = false;
                $response->error = "fcmToken not provided in POST data";
            }
        } else {
            http_response_code(405);
            $response->error = "Method not allowed";
        }
    } else {
        http_response_code(400);
        $response->error = "patientID ID not provided";
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
