<?php

// Assuming you have a MySQL database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teleclinic";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the 'q' parameter is set
if (isset($_GET['q'])) {
    $searchQuery = $conn->real_escape_string($_GET['q']);

    // Assuming you're passing specialistID as a parameter
    $specialistID = $_GET['specialistID'];

    $sql = "SELECT consultation.*, patient.* FROM consultation INNER JOIN patient ON consultation.patientID = patient.patientID WHERE consultation.specialistID = ? AND consultation.consultationStatus = 'Accepted' AND patientName LIKE '%$searchQuery%' GROUP BY consultation.patientID";

    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $specialistID); // Assuming specialistID is an integer
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Fetch all clinic information

    // Assuming you're passing specialistID as a parameter
    $specialistID = $_GET['specialistID'];

    $sql = "SELECT consultation.*, patient.* FROM consultation INNER JOIN patient ON consultation.patientID = patient.patientID WHERE consultation.specialistID = ? AND consultation.consultationStatus = 'Accepted' GROUP BY consultation.patientID";

    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $specialistID); // Assuming specialistID is an integer
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($result->num_rows > 0) {
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo "0 results";
}

$stmt->close();
$conn->close();

?>
