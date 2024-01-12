<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
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
    $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

    try {
        if (empty($searchTerm)) {
            // Fetch all medication data from the database if search term is empty
            $selectStmt = $db->prepare("SELECT * FROM medicine");
        } else {
            // Search for medications based on MedGeneral, MedForm, and Dosage
            $selectStmt = $db->prepare("SELECT MedGeneral, MedForm, Dosage FROM medicine 
                                       WHERE MedGeneral LIKE :searchTerm 
                                          
                                       LIMIT 10");
            $selectStmt->bindValue(':searchTerm', "%$searchTerm%", PDO::PARAM_STR);
        }

        // Execute the query
        $selectStmt->execute();

        // Fetch the result as an associative array
        $medications = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        // Set the JSON content type header
        header('Content-Type: application/json');

        // Send the response as JSON with medication data or suggestions
        echo json_encode(["status" => "success", "data" => $medications]);
        exit;

    } catch (PDOException $ex) {
        http_response_code(500);
        $errorDetails = [
            "status" => "error",
            "message" => "Failed to retrieve medication information: " . $ex->getMessage(),
            "trace" => $ex->getTraceAsString(),
        ];
        echo json_encode($errorDetails);
        error_log("Exception in GET request: " . $ex->getMessage() . "\nTrace: " . $ex->getTraceAsString());
    }
}
?>
