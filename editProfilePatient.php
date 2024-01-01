<?php


    // Establish a connection to your MySQL database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "teleclinic";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the specialist ID from the query parameters
    $patientID = isset($_GET['patientID']) ? intval($_GET['patientID']) : 0;

    // Check if the image data is present in the request
    if (($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
        // Read the image file
        $imageData = file_get_contents($_FILES['image']['tmp_name']);

        // Debugging: Log SQL queries
        echo "SQL Query: UPDATE patient SET profileImage = ? WHERE patientID = $patientID\n";

        // Debugging: Log image data
        file_put_contents('debug_patientimage.txt', print_r($_FILES['image'], true));
        file_put_contents('debug_patientimageData.txt', $imageData);

echo '<pre>';
var_dump($_FILES['image']);
echo '</pre>';

        // Prepare the SQL statement with a parameter placeholder
        $stmt = $conn->prepare("UPDATE patient SET profileImage = ? WHERE patientID = ?");
        
        // Bind the parameters
        $stmt->bind_param("si", $imageData, $patientID);

if ($stmt->execute()) {
    // Save the image to a directory on your device with a unique filename
    $timestamp = time(); // Get current timestamp
    $imagePath = '/xampp/htdocs/teleclinic/patientImage/' . $patientID . '_profile_image_' . $timestamp . '.jpg';
    file_put_contents($imagePath, $imageData);

    // Return a JSON response
    echo json_encode(['status' => 'success', 'message' => 'Image uploaded successfully']);
} else {
    // Return a JSON response
    echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
}

        // Close the statement
        $stmt->close();
    }

    // Check if specialistName is present in the request
    if (($_POST['patientName'])) {
        $patientName = $_POST['patientName'];

        // Update patientName in the database
        $sql = "UPDATE patient SET patientName = ? WHERE patientID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $patientName, $patientID);
        $stmt->execute();

        // Check for SQL errors
        if ($stmt->error) {
            // Debugging: Print the SQL error message
            die('Error: ' . $stmt->error);
        }

        $stmt->close();
    }

    // Check if specialistTitle is present in the request
    if (($_POST['icNumber'])) {
        $icNumber = $_POST['icNumber'];

        // Update specialistTitle in the database
        $sql = "UPDATE patient SET icNumber = ? WHERE patientID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $icNumber, $patientID);
        $stmt->execute();

        // Check for SQL errors
        if ($stmt->error) {
            // Debugging: Print the SQL error message
            die('Error: ' . $stmt->error);
        }

        $stmt->close();
    }

    // Check if phone is present in the request
    if (($_POST['phone'])) {
        $phone = $_POST['phone'];

        // Update phone in the database
        $sql = "UPDATE patient SET phone = ? WHERE patientID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $phone, $patientID);
        $stmt->execute();

        // Check for SQL errors
        if ($stmt->error) {
            // Debugging: Print the SQL error message
            die('Error: ' . $stmt->error);
        }

        $stmt->close();
    }

  if (($_POST['gender'])) {
        $gender = $_POST['gender'];

        // Update specialistName in the database
        $sql = "UPDATE patient SET gender = ? WHERE patientID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $gender, $patientID);
        $stmt->execute();

        // Check for SQL errors
        if ($stmt->error) {
            // Debugging: Print the SQL error message
            die('Error: ' . $stmt->error);
        }

        $stmt->close();
    }

    // Check if specialistTitle is present in the request
    if (($_POST['birthDate'])) {
        $birthDate = $_POST['birthDate'];

        // Update patient in the database
        $sql = "UPDATE patient SET birthDate = ? WHERE patientID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $birthDate, $patientID);
        $stmt->execute();

        // Check for SQL errors
        if ($stmt->error) {
            // Debugging: Print the SQL error message
            die('Error: ' . $stmt->error);
        }

        $stmt->close();
    }

   

    // Close the connection
    $conn->close();
    
    // Set the content type header to application/json
    header('Content-Type: application/json');

    // Return a JSON response
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully', 'patientID' => $patientID]);
} else {
    // Set the content type header to application/json
    header('Content-Type: application/json');

    // Return a JSON response
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}



?>