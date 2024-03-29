<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: image/jpeg');

$hostname = "localhost";
$database = "teleclinic";
$username = "root";
$password = "";

try {
    $db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection error: " . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $specialistID = isset($_GET['specialistID']) ? $_GET['specialistID'] : null;
$patientID = isset($_GET['patientID']) ? $_GET['patientID'] : null;

    error_log("Received GET_IMAGE request for specialistID: $specialistID");

    if ($specialistID === null) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "specialistID is required"]);
        exit;
    }
  error_log("Received GET_IMAGE request for patientID: $patientID");

    if ($patientID === null) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "patientID is required"]);
        exit;
    }

    try {
        // Fetch only the image data from the database
        $selectStmt = $db->prepare("SELECT consultation.*, patient.profileImage
    FROM consultation
    INNER JOIN patient ON consultation.patientID = patient.patientID
    WHERE consultation.specialistID = :specialistID
      AND consultation.patientID = :patientID
      AND consultation.consultationStatus = 'Accepted' OR 'Done'
    GROUP BY consultation.patientID");

        $selectStmt->bindParam(':specialistID', $specialistID, PDO::PARAM_INT);
    $selectStmt->bindParam(':patientID', $patientID, PDO::PARAM_INT);

        $selectStmt->execute();

        // Fetch the result as an associative array
        $imageData = $selectStmt->fetch(PDO::FETCH_ASSOC);

        // Check if image data is available
        if (!$imageData || !isset($imageData['profileImage'])) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Image not found"]);
            exit;
        }

        // Set the appropriate content type for the image
        header('Content-Type: image/jpeg'); // Change to the appropriate image type (jpeg, png, etc.)

        // Output the binary image data directly
        echo $imageData['profileImage'];
        exit;

    } catch (PDOException $ex) {
    http_response_code(500);
    $errorDetails = [
        "status" => "error",
        "message" => "Failed to retrieve specialistID image: " . $ex->getMessage(),
        "trace" => $ex->getTraceAsString(),
        "sql_query" => $selectStmt->queryString,  // Log the SQL query
    ];
    echo json_encode($errorDetails);
    error_log("Exception in GET_IMAGE request: " . $ex->getMessage() . "\nTrace: " . $ex->getTraceAsString());
}
}
?>
