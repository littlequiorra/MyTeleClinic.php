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

$response = new stdClass();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $specialistID = isset($_GET['specialistID']) ? $_GET['specialistID'] : null;
    error_log("Received GET request for specialistID: $specialistID");

    if ($specialistID === null) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "specialistID is required"]);
        exit;
    }

    try {
        $selectStmt = $db->prepare("SELECT * 
                                   FROM specialist 
                                   WHERE specialistID = :specialistID");
        $selectStmt->bindParam(':specialistID', $specialistID, PDO::PARAM_INT);
        $selectStmt->execute();

        $errorInfo = $selectStmt->errorInfo();
        if ($errorInfo[0] !== '00000') {
            $errorMessage = "Failed to execute GET query: " . $errorInfo[2];
            error_log("Error in GET request: $errorMessage");
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $errorMessage]);
            exit;
        }

        $specialistData = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => "success", "data" => $specialistData]);
        exit;
    } catch (PDOException $ex) {
        http_response_code(500);
        $errorDetails = [
            "status" => "error",
            "message" => "Failed to retrieve specialist information: " . $ex->getMessage(),
            "trace" => $ex->getTraceAsString(),
        ];
        echo json_encode($errorDetails);
        error_log("Exception in GET request: " . $ex->getMessage() . "\nTrace: " . $ex->getTraceAsString());
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        $requestData = json_decode(file_get_contents("php://input"), true);

        // Retrieve specialistID from the URL
        $specialistID = isset($_GET['specialistID']) ? $_GET['specialistID'] : null;

        // Log the received data
        error_log("Received Data - specialistID: $specialistID");

        // Fetch existing data from the database
        $selectStmt = $db->prepare("SELECT specialistName, phone, specialistTitle FROM specialist WHERE specialistID = :specialistID");
        $selectStmt->bindParam(':specialistID', $specialistID, PDO::PARAM_INT);
        $selectStmt->execute();

        $existingData = $selectStmt->fetch(PDO::FETCH_ASSOC);

        // Initialize variables for the SET clause and update data
        $setClause = "";
        $updateData = [];

        // Handle specialistName
        if (($requestData['specialistName']) && $requestData['specialistName'] !== $existingData['specialistName']) {
            $setClause .= "specialistName = :specialistName, ";
            $updateData[':specialistName'] = $requestData['specialistName'];
        }

        // Handle phone
        if (($requestData['phone']) && $requestData['phone'] !== $existingData['phone']) {
            $setClause .= "phone = :phone, ";
            $updateData[':phone'] = $requestData['phone'];
        }

        // Handle specialistTitle
        if (($requestData['specialistTitle']) && $requestData['specialistTitle'] !== $existingData['specialistTitle']) {
            $setClause .= "specialistTitle = :specialistTitle, ";
            $updateData[':specialistTitle'] = $requestData['specialistTitle'];
        }

        // Remove the trailing comma and space from SET clause
        $setClause = rtrim($setClause, ', ');

        // Check if any updates are needed
        if (!empty($updateData)) {
            // Prepare and execute the SQL query
            $queryString = "UPDATE specialist SET $setClause WHERE specialistID = :specialistID";
            $updateSpecialistStmt = $db->prepare($queryString);

            // Bind parameters from the update data array
            $updateSpecialistStmt->bindParam(':specialistID', $specialistID, PDO::PARAM_INT);

            // Bind parameters for each field
            if ($updateData[':specialistName']){
                $updateSpecialistStmt->bindParam(':specialistName', $updateData[':specialistName']);
            }
            if ($updateData[':phone']) {
                $updateSpecialistStmt->bindParam(':phone', $updateData[':phone']);
            }
            if ($updateData[':specialistTitle']){
                $updateSpecialistStmt->bindParam(':specialistTitle', $updateData[':specialistTitle']);
            }

            // Execute the prepared statement
            if ($updateSpecialistStmt->execute()) {
                $response->status = "success";
                // Log success
                error_log("Successfully updated specialist information");
            } else {
                $response->status = "error";
                $response->message = "Failed to update specialist information";
                // Log failure
                $errorInfo = $updateSpecialistStmt->errorInfo();
                error_log("Failed to execute PUT query: " . $errorInfo[2]);
            }
        } else {
            $response->status = "success";
            $response->message = "No updates needed";
        }
    } catch (PDOException $ex) {
        $response->status = "error";
        $response->message = "Exception: " . $ex->getMessage();
        $response->trace = $ex->getTraceAsString();

        // Log the exception details to the PHP error log
        error_log("Exception: " . $ex->getMessage() . "\nTrace: " . $ex->getTraceAsString());
    }

    // Output the response as JSON
    echo json_encode($response);
    exit;
}


?>
