<?php
session_start();
require 'database.php';
header("Content-Type: application/json"); 
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);
//if not logged in, pass failure value
if(!isset($_SESSION['user_id'])){
    echo json_encode(array(
		"success" => false,
		"message" => "Not Logged In"
	));
    exit();
}
if (empty($json_str)){
    exit();
}
//Variables can be accessed as such:
$userid = $_SESSION['user_id'];
$eventTitle = $json_obj['etitle'];
$eventContent = $json_obj['econtent'];
$formatted_date=$json_obj['edate'];
$eventStartTime=$json_obj['etime_start'];
$eventEndTime=$json_obj['etime_end'];
$token_r=$json_obj['token'];
//see if the token transferred through form match that stored in session and prevent CSRF forgery
if(!hash_equals($_SESSION['token'], $token_r)){
    die("Request forgery detected");
}

//create a new event and insert into database for the user himself
$stmt = $mysqli->prepare("INSERT into myEvent (userId, title, content, eventdate, startTime, endTime, important) values (?, ?, ?, ?, ?, ?, 0) ");
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    echo json_encode(array(
		"success" => false,
		"message" => "Event Creation Failed"
	));
    exit;
}
//echo $userid.$eventTitle.$eventContent.$formatted_date.$eventStartTime.$eventEndTime;   
$stmt->bind_param('isssss', $userid, $eventTitle, $eventContent, $formatted_date, $eventStartTime, $eventEndTime);
$stmt->execute();
$stmt->close();
echo json_encode(array(
    "success" => true
));

?>