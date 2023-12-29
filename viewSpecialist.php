<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "teleclinic";

  // Give your table name
    $table = "specialist"; // lets create a table named Employees.

    // Create Connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check Connection
    if($conn->connect_error){
        die("Connection Failed: " . $conn->connect_error);
        return;
    }

    // Get all records from the database
    $sql = "SELECT * from $table ";
    $db_data = array();
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $db_data[] = $row;
        }
        // Send back the complete records as a json
        echo json_encode($db_data);
    }else{
        echo "error";
    }
    $conn->close();

    return;

?>