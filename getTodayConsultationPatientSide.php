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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Set the timezone to Malaysia (Asia/Kuala_Lumpur)
        date_default_timezone_set('Asia/Kuala_Lumpur');

        // Create a MySQLi connection
        $conn = new mysqli($hostname, $username, $password, $database);

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Use CURDATE() to get the current date without the time part
        $today = date("Y-m-d");

        $patientID = isset($_GET['patientID']) ? $_GET['patientID'] : null;

        // Check if 'patientID' is not null before using it
        if ($patientID !== null) {
            // Prepare the SQL statement with a JOIN operation and ORDER BY
           $stmt = $conn->prepare("SELECT c.*, s.specialistName
                       FROM consultation c
                       LEFT JOIN specialist s ON c.specialistID = s.specialistID
                       WHERE c.consultationDateTime >= ? AND c.consultationDateTime < DATE_ADD(?, INTERVAL 1 DAY) AND c.patientID = ?
                       ORDER BY c.consultationDateTime ASC");

$stmt->bind_param("sss", $today, $today, $patientID);

            // Execute the statement
            if (!$stmt->execute()) {
                die("Error: " . $stmt->error);
            }

            // Get the result
            $result = $stmt->get_result();

            // Fetch the data
            $data = $result->fetch_all(MYSQLI_ASSOC) ?: [];

            $response->data = $data;
            http_response_code(200);
        } else {
            http_response_code(400);
            $response->error = "Patient ID not provided";
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
}
?>
