<?php

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the specialist ID from the query parameters
    $specialistID = isset($_GET['specialistID']) ? intval($_GET['specialistID']) : 0;

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

    // Check if the image data is present in the request
    if (($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
        // Read the image file
        $imageData = file_get_contents($_FILES['image']['tmp_name']);

        // Debugging: Log SQL queries
        echo "SQL Query: UPDATE specialist SET profileImage = ? WHERE specialistID = $specialistID\n";

        // Debugging: Log image data
        file_put_contents('debug_image.txt', print_r($_FILES['image'], true));
        file_put_contents('debug_imageData.txt', $imageData);

        // Prepare the SQL statement with a parameter placeholder
        $stmt = $conn->prepare("UPDATE specialist SET profileImage = ? WHERE specialistID = ?");
        
        // Bind the parameters
        $stmt->bind_param("si", $imageData, $specialistID);

  
        // Execute the statement
        if ($stmt->execute()) {
            $timestamp = time();
            $imagePath = '/xampp/htdocs/teleclinic/specialistImage/' . $specialistID . '_profile_image_' . $timestamp . '.jpg';
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
    if (($_POST['specialistName'])) {
        $specialistName = $_POST['specialistName'];

        // Update specialistName in the database
        $sql = "UPDATE specialist SET specialistName = ? WHERE specialistID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $specialistName, $specialistID);
        $stmt->execute();

        // Check for SQL errors
        if ($stmt->error) {
            // Debugging: Print the SQL error message
            die('Error: ' . $stmt->error);
        }

        $stmt->close();
    }

    // Check if specialistTitle is present in the request
    if (($_POST['specialistTitle'])) {
        $specialistTitle = $_POST['specialistTitle'];

        // Update specialistTitle in the database
        $sql = "UPDATE specialist SET specialistTitle = ? WHERE specialistID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $specialistTitle, $specialistID);
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
        $sql = "UPDATE specialist SET phone = ? WHERE specialistID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $phone, $specialistID);
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
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
} else {
    // Set the content type header to application/json
    header('Content-Type: application/json');

    // Return a JSON response
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>