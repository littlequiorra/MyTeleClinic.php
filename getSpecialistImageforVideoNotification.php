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
    $consultationID = isset($_GET['consultationID']) ? $_GET['consultationID'] : null;
    error_log("Received GET_IMAGE request for consultationID: $consultationID");

    if ($consultationID === null) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "consultationID is required"]);
        exit;
    }

    try {
        // Fetch only the image data from the database
        $selectStmt = $db->prepare(" SELECT s.profileImage
        FROM consultation c
        JOIN specialist s ON c.specialistID = s.specialistID
        WHERE c.consultationID = :consultationID");
        $selectStmt->bindParam(':consultationID', $consultationID, PDO::PARAM_INT);
        $selectStmt->execute();

        // Fetch the result as an associative array
        $imageData = $selectStmt->fetch(PDO::FETCH_ASSOC);

        // Check if image data is available
        if (!$imageData || !isset($imageData['profileImage'])) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Image not found"]);
            exit;
        }

       // Change to the appropriate image type (jpeg, png, etc.)

 // Output the binary image data directly
        echo $imageData['profileImage'];
        exit;

    } catch (PDOException $ex) {
        http_response_code(500);
        $errorDetails = [
            "status" => "error",
            "message" => "Failed to retrieve specialist image: " . $ex->getMessage(),
            "trace" => $ex->getTraceAsString(),
        ];
        echo json_encode($errorDetails);
        error_log("Exception in GET_IMAGE request: " . $ex->getMessage() . "\nTrace: " . $ex->getTraceAsString());
    }
}
?>
