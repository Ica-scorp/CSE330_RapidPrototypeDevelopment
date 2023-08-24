<?php
// edit events in the database
session_start();
require 'database.php';
header("Content-Type: application/json"); 
$json_str = file_get_contents('php://input');
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

$userid = $_SESSION['user_id'];//using session userid
$eid = $json_obj['eid'];
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


//edit the corresponding event according to eventid
$stmt = $mysqli->prepare("UPDATE myEvent SET title=?, content=?, eventdate=?, startTime=?, endTime=? where id=?");
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    echo json_encode(array(
		"success" => false,
		"message" => "Event Edit Failed"
	));
    exit;
}
                
$stmt->bind_param('sssssi', $eventTitle, $eventContent, $formatted_date, $eventStartTime, $eventEndTime, $eid);
$stmt->execute();
$stmt->close();
echo json_encode(array(
    "success" => true
));

?>