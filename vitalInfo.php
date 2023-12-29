<?php
$hostname = "localhost";
$database = "teleclinic";
$username = "root";
$password = "";

$db = new PDO ("mysql:host=$hostname;dbname=$database",$username,$password);
//initial response code
//response code will be changed if the request goes into any of the process
http_response_code(404);
$response = new stdClass();

{
	$jsonbody = json_decode(file_get_contents('php://input'));
}

if($_SERVER["REQUEST_METHOD"] == "POST") {

	try{

		$stmt = $db->prepare("INSERT INTO vital_info (`patientID`,`weight`,`height`,`bmi`,`waistCircumference`,
		`bloodPressure`,`bloodGlucose`,`heartRate`,`latestDate`)
		VALUES (:patientID, :weight, :height,:bmi, :waistCircumference, :bloodPressure, :bloodGlucose, :heartRate,
		:latestDate)");
		$stmt->execute(array(':patientID' => $jsonbody->patientID,':weight' => $jsonbody->weight,':height' => $jsonbody->height,
		':bmi' => $jsonbody->bmi,':waistCircumference' => $jsonbody->waistCircumference, ':bloodPressure' => $jsonbody->bloodPressure,
		':bloodGlucose' => $jsonbody->bloodGlucose, ':heartRate' => $jsonbody->heartRate,':latestDate' => $jsonbody->latestDate));
		http_response_code(200);
	}catch(Exception $ee) {
		http_response_code(500);
		$response->error = "Error occured ". $ee->getMessage();
	}
}
else if($_SERVER["REQUEST_METHOD"] == "GET"){
	try{
		$stmt = $db->prepare("SELECT * FROM vital_info");
		$stmt->execute();
		$response = $stmt->fetchAll(PDO::FETCH_ASSOC);
		http_response_code(200);
	}catch(Exception $ee) {
		http_response_code(500);
		$response->error = "Error occured". $ee->getMessage();
	}
}

echo json_encode($response);
exit();
?>