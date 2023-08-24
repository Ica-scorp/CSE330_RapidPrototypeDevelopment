<?php
    session_start();
    require "database.php";
?>

<?php 
    //return the number of events on a specific day for a user
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
    $eventdate_r = new DateTime($json_obj['date']);
    $formatted_date = $eventdate_r->format('Y-m-d');//convert to format that can be inserted into mySQL
    $userid_r = $json_obj['userid'];
    $token_r=$json_obj['token'];
    //see if the token transferred through form match that stored in session and prevent CSRF forgery
    if(!hash_equals($_SESSION['token'], $token_r)){
        die("Request forgery detected");
    }
    //select number of events that belongs to the user and on that date
    $stmt = $mysqli->prepare("SELECT COUNT(*), important from myEvent where eventdate=? AND userId=?");
    if(!$stmt){
        echo json_encode(array(
            "success" => false,
            "message" => "Event Search Failed"
        ));
        exit;
    }
    $stmt->bind_param('si', $formatted_date, $userid_r);
    $stmt->execute();
    $stmt->bind_result($count, $important);
    $flag = 0;
    while($stmt->fetch()){
        if($important == 1){
            $flag = 1;
        }
    }
    $stmt->close();
    //return the number of events



    $stmt = $mysqli->prepare("SELECT COUNT(*), important from myEvent join userRelations on (userRelations.userIdLinked = myEvent.userId) where userRelations.userId = ? and myEvent.eventdate = ?");
    if(!$stmt){
        echo json_encode(array(
            "success" => false,
            "message" => "Event Linked Search Failed"
        ));
        exit;
    }
    $stmt->bind_param('is', $userid_r, $formatted_date);
    $stmt->execute();
    $stmt->bind_result($countother, $important);
    while($stmt->fetch()){
        if($important == 1){
            $flag = 1;
        }
    }
    $count=$count+$countother;
    $stmt->close();

    echo json_encode(array(
        "count" => $count,
        "important" => $flag,
    ));
    exit;
?>