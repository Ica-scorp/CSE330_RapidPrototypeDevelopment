<?php
// change events' importance marks
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
//Variables can be accessed as such:
$userid = $_SESSION['user_id'];
$eid = $json_obj['eid'];
$token_r=$json_obj['token'];
//see if the token transferred through form match that stored in session and prevent CSRF forgery
if(!hash_equals($_SESSION['token'], $token_r)){
    die("Request forgery detected");
}
//change the importance mark of that specific event
$stmt = $mysqli->prepare("SELECT important from myEvent where id=?");
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    echo json_encode(array(
		"success" => false,
		"message" => "Event Edit Failed"
	));
    exit;
}
                
$stmt->bind_param('i',$eid);
$stmt->execute();
$stmt->bind_result($important);
$stmt->fetch();
$stmt->close();

if($important == 0){
    $important = 1;
}
else{
    $important = 0;
}
//update the importance mark for events for opposite value(0 to 1, 1 to 0)
$stmt = $mysqli->prepare("UPDATE myEvent SET important = ? where id=?");
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    echo json_encode(array(
		"success" => false,
		"message" => "Event Edit Failed"
	));
    exit;
}
                
$stmt->bind_param('ii', $important, $eid);
$stmt->execute();
$stmt->close();
echo json_encode(array(
    "important" => $important,
));

?>