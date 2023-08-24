<?php
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
$userid = $_SESSION['user_id'];
$eventId = $json_obj['eid'];
$token_r=$json_obj['token'];
//see if the token transferred through form match that stored in session and prevent CSRF forgery
if(!hash_equals($_SESSION['token'], $token_r)){
    die("Request forgery detected");
}
//delete the corresponding events
$stmt = $mysqli->prepare("delete from myEvent where id=? and userId=?");
if(!$stmt){
    echo json_encode(array(
		"success" => false,
		"message" => "Query Prep Failed"
	));
	exit;
}

$stmt->bind_param('ii', $eventId, $userid);
$stmt->execute();
$stmt->close();
echo json_encode(array(
    "success" => true
));

?>